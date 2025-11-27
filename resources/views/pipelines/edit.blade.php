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
                    <div class="col-md-5">
                        <input type="text" name="name" class="form-control"
                               placeholder="Stage name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="probability" class="form-control"
                               placeholder="Probability %" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary">Add Stage</button>
                    </div>
                </form>

                {{-- Reorder stages (positions) --}}
                @if($pipeline->stages->count())
                    <form method="POST" action="{{ route('pipelines.stages.reorder', $pipeline) }}">
                        @csrf

                        <div class="table-responsive mb-3">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Name</th>
                                        <th>Probability</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($pipeline->stages as $stage)
                                    <tr>
                                        <td style="width: 80px;">
                                            <input type="number"
                                                   name="positions[{{ $stage->id }}]"
                                                   class="form-control form-control-sm"
                                                   value="{{ $stage->position }}">
                                        </td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('pipelines.stages.update', [$pipeline, $stage]) }}"
                                                  class="d-flex align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name"
                                                       class="form-control form-control-sm me-2"
                                                       value="{{ $stage->name }}" required>
                                        </td>
                                        <td style="width: 140px;">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="probability"
                                                           class="form-control"
                                                           value="{{ $stage->probability }}"
                                                           min="0" max="100">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                        </td>
                                        <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary">
                                                    Save
                                                </button>
                                            </form>

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

                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            Save Order
                        </button>
                    </form>
                @else
                    <p class="text-muted mb-0">No stages yet. Add stages to define your pipeline.</p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
