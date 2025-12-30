@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-9">
    <div class="card">
      <div class="card-body">
        <h1 class="h5 mb-3">Edit Contact</h1>

        <form method="POST" action="{{ ws_route('contacts.update', $contact) }}">
          @method('PUT')
          @include('contacts._form', ['contact' => $contact, 'submitLabel' => 'Update Contact'])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
