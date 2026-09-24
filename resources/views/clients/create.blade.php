@extends('layouts.app')
@section('content')
    @include('clients._form', ['client' => null, 'action' => route('clients.store')])
@endsection
