@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-9">
    <div class="card">
      <div class="card-body">
        <h1 class="h5 mb-3">Edit Deal</h1>

        <form method="POST" action="{{ route('deals.update', $deal) }}">
          @method('PUT')
          @include('deals._form', ['deal' => $deal, 'submitLabel' => 'Update Deal'])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
