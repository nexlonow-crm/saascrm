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
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Acme Sales CRM"
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
