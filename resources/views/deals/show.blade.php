@extends('layouts.app')

@section('title', 'Deal: '.$deal->title)
@section('page-title', 'Deal Details')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row">
    {{-- Left: Deal info --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $deal->title }}</h5>
            </div>
            <div class="card-body">
                <p><strong>Amount:</strong> {{ $deal->amount }} {{ $deal->currency }}</p>
                <p><strong>Stage:</strong>
                    @if($deal->stage)
                        <span class="badge bg-{{ $deal->stage->badgeColor() }}">
                            {{ $deal->stage->displayName() }}
                        </span>
                    @endif
                </p>
                <p><strong>Status:</strong> {{ ucfirst($deal->status) }}</p>
                @if($deal->expected_close_date)
                    <p><strong>Expected close:</strong> {{ $deal->expected_close_date->format('Y-m-d') }}</p>
                @endif
                @if($deal->company)
                    <p><strong>Company:</strong> {{ $deal->company->name }}</p>
                @endif
                @if($deal->primaryContact)
                    <p><strong>Primary contact:</strong> {{ $deal->primaryContact->full_name }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Activities --}}
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Activities</h5>
            </div>
            <div class="card-body">
                {{-- Create form --}}
                @include('activities._create', ['subject' => $deal])

                {{-- List --}}
                @include('activities._list', ['subject' => $deal])
            </div>
        </div>
    </div>
</div>
@endsection
