@csrf

<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control"
             value="{{ old('name', $company->name ?? '') }}" required>
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Domain</label>
      <input type="text" name="domain" class="form-control"
             value="{{ old('domain', $company->domain ?? '') }}"
             placeholder="example.com">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Website</label>
      <input type="text" name="website" class="form-control"
             value="{{ old('website', $company->website ?? '') }}"
             placeholder="https://...">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control"
             value="{{ old('phone', $company->phone ?? '') }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Industry</label>
      <input type="text" name="industry" class="form-control"
             value="{{ old('industry', $company->industry ?? '') }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Company Size</label>
      <input type="text" name="size" class="form-control"
             value="{{ old('size', $company->size ?? '') }}"
             placeholder="1-10, 11-50, ...">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Street</label>
      <input type="text" name="street" class="form-control"
             value="{{ old('street', $company->street ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">City</label>
      <input type="text" name="city" class="form-control"
             value="{{ old('city', $company->city ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">State</label>
      <input type="text" name="state" class="form-control"
             value="{{ old('state', $company->state ?? '') }}">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Postal Code</label>
      <input type="text" name="postal_code" class="form-control"
             value="{{ old('postal_code', $company->postal_code ?? '') }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label">Country</label>
      <input type="text" name="country" class="form-control"
             value="{{ old('country', $company->country ?? '') }}">
    </div>
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
<a href="{{ ws_route('companies.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
