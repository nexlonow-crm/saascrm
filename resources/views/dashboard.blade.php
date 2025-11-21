@extends('layouts.app')

@section('content')
<div class="row g-3">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="text-muted text-uppercase small mb-1">Contacts</div>
        <div class="h4 mb-0">{{ \App\Domain\Contacts\Models\Contact::count() }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="text-muted text-uppercase small mb-1">Open Deals</div>
        <div class="h4 mb-0">{{ \App\Domain\Deals\Models\Deal::where('status','open')->count() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
