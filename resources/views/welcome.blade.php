@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4 mb-2">Welcome to {{ config('app.name') }}</h1>
        <p class="mb-0 text-muted">Your CRM SaaS is now running with Laravel + Bootstrap.</p>
    </div>
</div>
@endsection
