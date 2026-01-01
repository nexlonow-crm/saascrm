@extends('layouts.app')

@section('title', 'Create Workspace')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h4>Create Your Workspace</h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('workspaces.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Workspace Name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Acme Sales CRM"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Industry Template</label>
                        <select name="industry_key"
                                class="form-select @error('industry_key') is-invalid @enderror">
                            @foreach(config('workspace_templates.options') as $key => $label)
                                <option value="{{ $key }}"
                                    {{ old('industry_key', config('workspace_templates.default')) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('industry_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            This will preload a pipeline and stages for your niche.
                        </div>
                    </div>

                    <button class="btn btn-primary">
                        Create Workspace
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
