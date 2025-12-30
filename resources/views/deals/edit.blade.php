{{-- resources/views/deals/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Deal')
@section('page-title', 'Edit Deal')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ ws_route('deals.update', $deal) }}">
            @csrf
            @method('PUT')
            @include('deals._form', [
                'deal'      => $deal,
                'pipelines' => $pipelines,
                'companies' => $companies,
                'contacts'  => $contacts,
            ])
            <button type="submit" class="btn btn-primary mt-3">Update Deal</button>
        </form>
    </div>
</div>
@endsection
