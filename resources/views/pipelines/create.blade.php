@extends('layouts.app')

@section('title', 'Create Pipeline')

@section('page-title', 'Create Pipeline')

@section('content')
<div class="row">
    <div class="col-lg-7 col-xl-6">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">New Pipeline</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('pipelines.store', ['workspace' => $ws->slug]) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Pipeline Name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Sales Pipeline"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type (optional)</label>
                        <input type="text"
                               name="type"
                               value="{{ old('type') }}"
                               class="form-control @error('type') is-invalid @enderror"
                               placeholder="sales / job_search / personal">
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Used for templates/industry presets later.</div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="checkbox"
                               name="is_default"
                               id="is_default"
                               value="1"
                               {{ old('is_default') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_default">
                            Set as default pipeline for this workspace
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Create Pipeline</button>
                        <a href="{{ route('pipelines.index', ['workspace' => $ws->slug]) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            After creating a pipeline, go to Edit to add stages (and reorder).
        </div>

    </div>
</div>
@endsection
