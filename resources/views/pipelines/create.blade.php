@extends('layouts.app')

@section('title', 'Create Pipeline')
@section('page-title', 'Create Pipeline')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('pipelines.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Type (optional)</label>
                <input type="text" name="type" class="form-control"
                       value="{{ old('type') }}" placeholder="sales, job_search, personal, ...">
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1"
                       id="is_default" name="is_default"
                       @checked(old('is_default'))>
                <label class="form-check-label" for="is_default">
                    Set as default pipeline
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Save Pipeline</button>
            <a href="{{ route('pipelines.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
