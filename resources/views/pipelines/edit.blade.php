@extends('layouts.app')

@section('title', 'Edit Pipeline')
@section('page-title', 'Edit Pipeline: '.$pipeline->name)

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<style>
    .drag-handle {
    cursor: grab;
    padding-left: 6px;
}

.drag-handle:hover {
    color: #666;
}
</style>
<div class="row">
    {{-- Pipeline info --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Pipeline Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pipelines.update', $pipeline) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $pipeline->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" name="type" class="form-control"
                               value="{{ old('type', $pipeline->type) }}"
                               placeholder="sales, job_search, personal, ...">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox"
                               id="is_default" name="is_default" value="1"
                               @checked(old('is_default', $pipeline->is_default))>
                        <label class="form-check-label" for="is_default">
                            Set as default pipeline
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Save Pipeline
                    </button>
                    <a href="{{ route('pipelines.index') }}" class="btn btn-outline-secondary ms-2">
                        Back
                    </a>
                </form>
            </div>
        </div>
    </div>

    {{-- Stages management --}}
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Stages</h5>
            </div>
            <div class="card-body">

                {{-- Add new stage --}}
                <form method="POST" action="{{ route('pipelines.stages.store', $pipeline) }}" class="row g-2 mb-4">
                    @csrf
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control"
                               placeholder="Stage name (e.g. Qualified)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="label" class="form-control"
                               placeholder="Label (Warm, Hot)">
                    </div>
                    <div class="col-md-3">
                        <select name="badge_color" class="form-select">
                            <option value="">Color</option>
                            <option value="secondary">Secondary</option>
                            <option value="primary">Primary</option>
                            <option value="success">Success</option>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="probability" class="form-control"
                               placeholder="%" min="0" max="100">
                    </div>
                    <div class="col-12 mt-2">
                        <button class="btn btn-primary btn-sm">Add Stage</button>
                    </div>
                </form>

                {{-- Stages list --}}
                @if($pipeline->stages->count())
                    <div class="table-responsive mb-3">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"></th> {{-- drag handle --}}
                                    <th style="width: 80px;">Pos</th>
                                    <th style="width: 160px;">Name</th>
                                    <th style="width: 160px;">Label</th>
                                    <th style="width: 160px;">Color</th>
                                    <th style="width: 120px;">Prob.</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="stages-sortable">
                            @foreach($pipeline->stages as $stage)
                                {{-- Hidden form for this stage (edit fields) --}}
                                <form id="stage-{{ $stage->id }}"
                                      method="POST"
                                      action="{{ route('pipelines.stages.update', [$pipeline, $stage]) }}">
                                    @csrf
                                    @method('PUT')
                                </form>

                                <tr data-stage-id="{{ $stage->id }}">
                                    <td class="text-muted drag-handle" style="cursor: grab;">
                                    <i data-feather="menu"></i>
                                </td>
                                    <td>
                                        <input type="number"
                                               name="position"
                                               form="stage-{{ $stage->id }}"
                                               class="form-control form-control-sm"
                                               value="{{ $stage->position }}">
                                    </td>

                                    <td>
                                        <input type="text"
                                               name="name"
                                               form="stage-{{ $stage->id }}"
                                               class="form-control form-control-sm"
                                               value="{{ $stage->name }}" required>
                                    </td>

                                    <td>
                                        <input type="text"
                                               name="label"
                                               form="stage-{{ $stage->id }}"
                                               class="form-control form-control-sm"
                                               value="{{ $stage->label }}"
                                               placeholder="e.g. Warm, Hot">
                                    </td>

                                    <td>
                                        <select name="badge_color"
                                                form="stage-{{ $stage->id }}"
                                                class="form-select form-select-sm">
                                            @php
                                                $colors = ['secondary','primary','success','info','warning','danger','dark'];
                                            @endphp
                                            <option value="">Default</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color }}"
                                                    @selected($stage->badge_color === $color)>
                                                    {{ ucfirst($color) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number"
                                                   name="probability"
                                                   form="stage-{{ $stage->id }}"
                                                   class="form-control"
                                                   value="{{ $stage->probability }}"
                                                   min="0" max="100">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <button type="submit"
                                                form="stage-{{ $stage->id }}"
                                                class="btn btn-sm btn-outline-primary mb-1">
                                            Save
                                        </button>

                                        <form method="POST"
                                              action="{{ route('pipelines.stages.destroy', [$pipeline, $stage]) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this stage?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Save order bottom bar (hidden by default) --}}
                    <form id="stage-order-form"
                        method="POST"
                        action="{{ route('pipelines.stages.reorder', $pipeline) }}"
                        class="stage-order-bar d-none">
                        @csrf
                        <input type="hidden" name="order" id="stages-order-input">

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                You have unsaved stage order changes
                            </div>
                            <div>
                                <button type="button"
                                        id="stage-order-cancel"
                                        class="btn btn-link btn-sm text-muted me-2">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="btn btn-dark btn-sm">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>

                @else
                    <p class="text-muted mb-0">No stages yet. Add stages to define your pipeline.</p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tbody      = document.getElementById('stages-sortable');
            const orderForm  = document.getElementById('stage-order-form');
            const orderInput = document.getElementById('stages-order-input');
            const cancelBtn  = document.getElementById('stage-order-cancel');

            if (!tbody || !orderForm || !orderInput || typeof Sortable === 'undefined') {
                return;
            }

            let dirty = false;

            Sortable.create(tbody, {
                animation: 150,
                handle: ".drag-handle",
                onEnd: function () {
                    dirty = true;
                    orderForm.classList.remove('d-none');
                }
            });

            orderForm.addEventListener('submit', function () {
                const ids = Array.from(tbody.querySelectorAll('tr[data-stage-id]'))
                    .map(function (row) {
                        return row.getAttribute('data-stage-id');
                    });

                orderInput.value = ids.join(',');
            });

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    // Reload page to reset order back to server values
                    window.location.reload();
                });
            }
        });
    </script>
@endpush

