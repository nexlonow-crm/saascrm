@extends('layouts.app')

@section('title', 'Deals Board')
@section('page-title', 'Deals Kanban Board')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Pipeline: {{ $pipeline->name }}</h5>

    <form method="GET" action="{{ route('deals.board') }}" class="d-flex align-items-center">
        <label class="me-2 mb-0">Pipeline</label>
        <select name="pipeline_id" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach($pipelines as $pl)
                <option value="{{ $pl->id }}" @selected($pl->id === $pipeline->id)>
                    {{ $pl->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<div class="row flex-nowrap overflow-auto" style="white-space: nowrap;">
    @foreach($pipeline->stages as $stage)
        @php
            $stageDeals = $dealsByStage->get($stage->id) ?? collect();
        @endphp

        <div class="col-md-3 col-lg-3 col-xl-2" style="min-width: 260px;">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-{{ $stage->badge_color ?? 'secondary' }} me-2">
                            {{ $stage->label ?? $stage->name }}
                        </span>
                    </div>
                    <span class="badge bg-light text-muted">
                        {{ $stageDeals->count() }} deals
                    </span>
                </div>
                <div class="card-body p-2">
                    <div class="kanban-stage"
                         data-stage-id="{{ $stage->id }}"
                         id="kanban-stage-{{ $stage->id }}">
                        @foreach($stageDeals as $deal)
                            <div class="card mb-2 shadow-sm kanban-deal"
                                 data-deal-id="{{ $deal->id }}"
                                 style="cursor: grab;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <strong class="small">
                                            <a href="{{ route('deals.show', $deal) }}">
                                                {{ $deal->title }}
                                            </a>
                                        </strong>
                                        @if($deal->amount)
                                            <span class="small text-muted">
                                                {{ $deal->amount }} {{ $deal->currency }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($deal->company)
                                        <div class="small text-muted">
                                            {{ $deal->company->name }}
                                        </div>
                                    @endif

                                    @if($deal->primaryContact)
                                        <div class="small text-muted">
                                            {{ $deal->primaryContact->full_name }}
                                        </div>
                                    @endif

                                    @if($deal->expected_close_date)
                                        <div class="small text-muted mt-1">
                                            Close: {{ $deal->expected_close_date->format('Y-m-d') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Empty state --}}
                    @if($stageDeals->isEmpty())
                        <p class="text-muted small mb-0 text-center py-2">
                            No deals
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const moveUrl = "{{ route('deals.kanban.move') }}";

            const stageElems = document.querySelectorAll('.kanban-stage');

            stageElems.forEach(function (elem) {
                Sortable.create(elem, {
                    group: 'deals-kanban',
                    animation: 150,
                    handle: '.kanban-deal',
                    onEnd: function (evt) {
                        const item = evt.item; // the dragged .kanban-deal
                        const dealId = item.getAttribute('data-deal-id');
                        const newStageElem = evt.to.closest('.kanban-stage');
                        const newStageId = newStageElem.getAttribute('data-stage-id');

                        if (!dealId || !newStageId) {
                            return;
                        }

                        // AJAX request to update deal's stage
                        fetch(moveUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                deal_id: dealId,
                                stage_id: newStageId
                            })
                        })
                        .then(function (response) {
                            if (!response.ok) {
                                console.error('Error moving deal', response);
                            }
                            return response.json();
                        })
                        .then(function (data) {
                            if (!data.ok) {
                                console.error('Move error:', data.message);
                            }
                        })
                        .catch(function (error) {
                            console.error('Request failed:', error);
                        });
                    }
                });
            });
        });
    </script>
@endpush
