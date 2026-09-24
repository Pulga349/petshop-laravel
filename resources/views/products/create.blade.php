@extends('layouts.app')
@section('content')
    @include('products._form', ['product' => null, 'action' => route('products.store'), 'suppliers' => $suppliers, 'categories' => $categories])
@endsection
