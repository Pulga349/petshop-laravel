@extends('layouts.app')
@section('content')
    @include('suppliers._form', ['supplier' => $supplier, 'action' => route('suppliers.update', $supplier)])
@endsection
