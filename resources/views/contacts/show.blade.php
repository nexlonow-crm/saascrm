@extends('layouts.app')

@section('title', 'Contact: '.$contact->full_name)
@section('page-title', 'Contact Details')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row">
    {{-- Left: Contact info --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $contact->full_name }}</h5>
            </div>
            <div class="card-body">
                @if($contact->email)
                    <p><strong>Email:</strong> {{ $contact->email }}</p>
                @endif
                @if($contact->phone)
                    <p><strong>Phone:</strong> {{ $contact->phone }}</p>
                @endif
                @if($contact->mobile)
                    <p><strong>Mobile:</strong> {{ $contact->mobile }}</p>
                @endif
                @if($contact->job_title)
                    <p><strong>Job title:</strong> {{ $contact->job_title }}</p>
                @endif
                @if($contact->company)
                    <p><strong>Company:</strong> {{ $contact->company->name }}</p>
                @endif

                @if($contact->street || $contact->city || $contact->country)
                    <hr>
                    <p class="mb-0">
                        <strong>Address:</strong><br>
                        {{ $contact->street }}<br>
                        {{ $contact->city }} {{ $contact->state }} {{ $contact->postal_code }}<br>
                        {{ $contact->country }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Optional: related deals --}}
        @if($contact->relationLoaded('deals') && $contact->deals->count())
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Deals</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($contact->deals as $deal)
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
        {{-- Timeline --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Timeline</h5>
            </div>
            <div class="card-body">
                @include('deals._timeline', ['timeline' => $timeline])
            </div>
        </div>

        {{-- Notes (optional, if you want separate card like deals) --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Notes</h5>
            </div>
            <div class="card-body">
                @include('notes._create', ['subject' => $contact])
                @include('notes._list', ['subject' => $contact])
            </div>
        </div>

        {{-- Activities --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Activities</h5>
            </div>
            <div class="card-body">
                @include('activities._create', ['subject' => $contact])
                @include('activities._list', ['subject' => $contact])
            </div>
        </div>

    </div>

</div>
@endsection
