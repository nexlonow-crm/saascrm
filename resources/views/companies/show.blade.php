@extends('layouts.app')

@section('title', 'Company: '.$company->name)
@section('page-title', 'Company Details')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row">
    {{-- Left: Company info --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $company->name }}</h5>
            </div>
            <div class="card-body">
                @if($company->website)
                    <p><strong>Website:</strong>
                        <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                    </p>
                @endif
                @if($company->phone)
                    <p><strong>Phone:</strong> {{ $company->phone }}</p>
                @endif

                @if($company->street || $company->city || $company->country)
                    <hr>
                    <p class="mb-0">
                        <strong>Address:</strong><br>
                        {{ $company->street }}<br>
                        {{ $company->city }} {{ $company->state }} {{ $company->postal_code }}<br>
                        {{ $company->country }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Contacts at this company --}}
        @if($company->relationLoaded('contacts') && $company->contacts->count())
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Contacts</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($company->contacts as $contact)
                            <li class="list-group-item px-0">
                                <a href="{{ route('contacts.show', $contact) }}">
                                    {{ $contact->full_name }}
                                </a>
                                @if($contact->email)
                                    <span class="text-muted small"> – {{ $contact->email }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Deals for this company --}}
        @if($company->relationLoaded('deals') && $company->deals->count())
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Deals</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($company->deals as $deal)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <a href="{{ route('deals.show', $deal) }}">
                                        {{ $deal->title }}
                                    </a>
                                    @if($deal->stage)
                                        <span class="badge bg-{{ $deal->stage->badgeColor() }} ms-2">
                                            {{ $deal->stage->displayName() }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    {{ $deal->amount }} {{ $deal->currency }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    {{-- Right: Activities --}}
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Activities</h5>
            </div>
            <div class="card-body">
                {{-- Create form --}}
                @include('activities._create', ['subject' => $company])

                {{-- List --}}
                @include('activities._list', ['subject' => $company])
            </div>
        </div>
    </div>
</div>
@endsection
