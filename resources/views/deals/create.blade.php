{{-- resources/views/deals/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Deal')
@section('page-title', 'Create Deal')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('deals.store') }}">
            @csrf
            @include('deals._form', [
                'deal'      => null,
                'pipelines' => $pipelines,
                'companies' => $companies,
                'contacts'  => $contacts,
            ])
            <button type="submit" class="btn btn-primary mt-3">Save Deal</button>
        </form>
    </div>
</div>
@endsection
