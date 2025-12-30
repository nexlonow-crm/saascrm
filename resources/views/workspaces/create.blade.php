@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Workspace</h1>

    <form method="POST" action="{{ route('workspaces.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Workspace Name</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
