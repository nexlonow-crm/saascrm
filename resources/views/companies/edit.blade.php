@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-9">
    <div class="card">
      <div class="card-body">
        <h1 class="h5 mb-3">Edit Company</h1>

        <form method="POST" action="{{ route('companies.update', $company) }}">
          @method('PUT')
          @include('companies._form', ['company' => $company, 'submitLabel' => 'Update Company'])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
