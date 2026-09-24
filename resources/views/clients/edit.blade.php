@extends('layouts.app')
@section('content')
    @include('clients._form', ['client' => $client, 'action' => route('clients.update', $client)])
@endsection
