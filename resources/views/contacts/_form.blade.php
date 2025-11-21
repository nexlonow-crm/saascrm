@csrf

<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">First Name</label>
      <input type="text" name="first_name" class="form-control"
             value="{{ old('first_name', $contact->first_name ?? '') }}" required>
      @error('first_name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Last Name</label>
      <input type="text" name="last_name" class="form-control"
             value="{{ old('last_name', $contact->last_name ?? '') }}" required>
      @error('last_name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control"
             value="{{ old('email', $contact->email ?? '') }}">
      @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control"
             value="{{ old('phone', $contact->phone ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Mobile</label>
      <input type="text" name="mobile" class="form-control"
             value="{{ old('mobile', $contact->mobile ?? '') }}">
    </div>
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Company</label>
  <select name="company_id" class="form-select">
    <option value="">— None —</option>
    @foreach($companies as $company)
      <option value="{{ $company->id }}"
        @selected(old('company_id', $contact->company_id ?? null) == $company->id)>
        {{ $company->name }}
      </option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Job Title</label>
  <input type="text" name="job_title" class="form-control"
         value="{{ old('job_title', $contact->job_title ?? '') }}">
</div>

<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Street</label>
      <input type="text" name="street" class="form-control"
             value="{{ old('street', $contact->street ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">City</label>
      <input type="text" name="city" class="form-control"
             value="{{ old('city', $contact->city ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">State</label>
      <input type="text" name="state" class="form-control"
             value="{{ old('state', $contact->state ?? '') }}">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Postal Code</label>
      <input type="text" name="postal_code" class="form-control"
             value="{{ old('postal_code', $contact->postal_code ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Country</label>
      <input type="text" name="country" class="form-control"
             value="{{ old('country', $contact->country ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Lifecycle Stage</label>
      <input type="text" name="lifecycle_stage" class="form-control"
             value="{{ old('lifecycle_stage', $contact->lifecycle_stage ?? 'lead') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Lead Source</label>
      <input type="text" name="lead_source" class="form-control"
             value="{{ old('lead_source', $contact->lead_source ?? '') }}">
    </div>
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Status</label>
  <select name="status" class="form-select">
    @php
      $status = old('status', $contact->status ?? 'active');
    @endphp
    <option value="active" @selected($status === 'active')>Active</option>
    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
    <option value="archived" @selected($status === 'archived')>Archived</option>
  </select>
</div>

<button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
<a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
