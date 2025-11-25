@csrf

<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control"
             value="{{ old('title', $deal->title ?? '') }}" required>
      @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Amount</label>
      <input type="number" step="0.01" name="amount" class="form-control"
             value="{{ old('amount', $deal->amount ?? '') }}">
      @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Currency</label>
      <input type="text" name="currency" class="form-control"
             value="{{ old('currency', $deal->currency ?? 'USD') }}"
             maxlength="3">
      @error('currency')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Pipeline</label>
      <select name="pipeline_id" class="form-select" required>
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
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Stage</label>
      <select name="stage_id" class="form-select" required>
        <option value="">— Select stage —</option>
        @foreach($pipelines as $pipeline)
          @foreach($pipeline->stages as $stage)
            <option value="{{ $stage->id }}"
              @selected(old('stage_id', $deal->stage_id ?? null) == $stage->id)>
              {{ $pipeline->name }} — {{ $stage->name }}
            </option>
          @endforeach
        @endforeach
      </select>
      @error('stage_id')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Status</label>
      @php
        $status = old('status', $deal->status ?? \App\Domain\Deals\Models\Deal::STATUS_OPEN);
      @endphp
      <select name="status" class="form-select">
        <option value="open" @selected($status === 'open')>Open</option>
        <option value="won" @selected($status === 'won')>Won</option>
        <option value="lost" @selected($status === 'lost')>Lost</option>
      </select>
      @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

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

<div class="row">
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Expected Close Date</label>
      <input type="date" name="expected_close_date" class="form-control"
             value="{{ old('expected_close_date', optional($deal->expected_close_date ?? null)->format('Y-m-d')) }}">
      @error('expected_close_date')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
<a href="{{ route('deals.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
