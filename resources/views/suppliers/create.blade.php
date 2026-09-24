@extends('layouts.app')
@section('content')
    @include('suppliers._form', ['supplier' => null, 'action' => route('suppliers.store')])
@endsection
