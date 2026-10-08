@extends('layouts.app')
@section('content')
    @include('products._form', ['product' => $product, 'action' => route('products.update', $product), 'suppliers' => $suppliers, 'categories' => $categories])
@endsection
