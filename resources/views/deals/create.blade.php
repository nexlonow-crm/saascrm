@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-9">
    <div class="card">
      <div class="card-body">
        <h1 class="h5 mb-3">Add Deal</h1>

        <form method="POST" action="{{ route('deals.store') }}">
          @include('deals._form', ['deal' => null, 'submitLabel' => 'Save Deal'])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
