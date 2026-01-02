@php
    // handy default
    $deal = $deal ?? null;
@endphp

<div class="row">
    <div class="col-md-8">
        {{-- Title --}}
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text"
                   name="title"
                   class="form-control"
                   value="{{ old('title', $deal->title ?? '') }}"
                   required>
            @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-4">
        {{-- Amount + Currency --}}
        <div class="row">
            <div class="col-7">
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number"
                           name="amount"
                           step="0.01"
                           class="form-control"
                           value="{{ old('amount', $deal->amount ?? '') }}">
                    @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-5">
                <div class="mb-3">
                    <label class="form-label">Currency</label>
                    <input type="text"
                           name="currency"
                           class="form-control"
                           maxlength="3"
                           value="{{ old('currency', $deal->currency ?? 'USD') }}">
                    @error('currency')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pipeline + Stage --}}
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Pipeline</label>
            <select name="pipeline_id" id="pipeline_id" class="form-select" required>
                <option value="">— Select pipeline —</option>
                @foreach($pipelines as $pipeline)
                    <option value="{{ $pipeline->id }}"
                        @selected(old('pipeline_id', $deal->pipeline_id ?? null) == $pipeline->id)>
                        {{ $pipeline->name }}
                    </option>
                @endforeach
            </select>
            @error('pipeline_id')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Stage</label>
            <select name="stage_id" id="stage_id" class="form-select" required>
                <option value="">— Select stage —</option>
                {{-- options will be injected by JS --}}
            </select>
            @error('stage_id')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- Company + Contact --}}
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Company</label>
            <select name="company_id" class="form-select">
                <option value="">— None —</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}"
                        @selected(old('company_id', $deal->company_id ?? null) == $company->id)>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
            @error('company_id')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Primary Contact</label>
            <select name="primary_contact_id" class="form-select">
                <option value="">— None —</option>
                @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}"
                        @selected(old('primary_contact_id', $deal->primary_contact_id ?? null) == $contact->id)>
                        {{ $contact->full_name }}
                    </option>
                @endforeach
            </select>
            @error('primary_contact_id')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- Expected close date + Status --}}
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Expected Close Date</label>
            <input type="date"
                   name="expected_close_date"
                   class="form-control"
                   value="{{ old('expected_close_date', optional($deal->expected_close_date ?? null)->format('Y-m-d')) }}">
            @error('expected_close_date')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            
    </div>
</div>

{{-- Description / Notes (optional) --}}
<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $deal->description ?? '') }}</textarea>
    @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

@push('scripts')
<script>
(function () {
    // Build a map: pipeline_id => [ { id, name }, ... ]
    const stagesByPipeline = @json(
        $pipelines->mapWithKeys(function ($pipeline) {
            return [
                $pipeline->id => $pipeline->stages->map(function ($stage) {
                    return ['id' => $stage->id, 'name' => $stage->name];
                })->values()
            ];
        })
    );

    const pipelineSelect = document.getElementById('pipeline_id');
    const stageSelect    = document.getElementById('stage_id');
    const currentStageId = @json(old('stage_id', $deal->stage_id ?? null));

    function populateStages(pipelineId) {
        // clear existing options
        stageSelect.innerHTML = '';

        // placeholder
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '— Select stage —';
        stageSelect.appendChild(placeholder);

        if (!pipelineId || !stagesByPipeline[pipelineId]) {
            return;
        }

        stagesByPipeline[pipelineId].forEach(function (stage) {
            const opt = document.createElement('option');
            opt.value = stage.id;
            opt.textContent = stage.name;
            if (Number(currentStageId) === Number(stage.id)) {
                opt.selected = true;
            }
            stageSelect.appendChild(opt);
        });
    }

    if (pipelineSelect && stageSelect) {
        // on change
        pipelineSelect.addEventListener('change', function () {
            populateStages(this.value);
        });

        // initial load (edit page or validation error)
        if (pipelineSelect.value) {
            populateStages(pipelineSelect.value);
        }
    }
})();
</script>
@endpush
