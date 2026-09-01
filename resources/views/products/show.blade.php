@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-200">Detalle de Producto: {{ $product->name }}</h2>
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Product Info -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-400">Nombre</p>
                    <p class="text-lg text-gray-200">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">SKU</p>
                    <p class="text-lg text-gray-200">{{ $product->sku ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Categoría</p>
                    <p class="text-lg text-gray-200">{{ $product->category ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Stock</p>
                    <p class="text-lg text-gray-200">{{ $product->getStock() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Precio de Venta</p>
                    <p class="text-lg text-emerald-400 font-semibold">${{ number_format($product->sale_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Precio de Compra</p>
                    <p class="text-lg text-gray-200">${{ number_format($product->purchase_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Proveedor</p>
                    <p class="text-lg text-gray-200">{{ $product->supplier->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Descripción</p>
                    <p class="text-lg text-gray-200">{{ $product->description ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Supplier Detail -->
        @if($product->supplier)
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-200 mb-4">Información del Proveedor</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-400">Nombre</p>
                    <p class="text-lg text-gray-200">{{ $product->supplier->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Contacto</p>
                    <p class="text-lg text-gray-200">{{ $product->supplier->contact_person ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Email</p>
                    <p class="text-lg text-gray-200">{{ $product->supplier->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Teléfono</p>
                    <p class="text-lg text-gray-200">{{ $product->supplier->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Dirección</p>
                    <p class="text-lg text-gray-200">{{ $product->supplier->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Categoría</p>
                    <p class="text-lg text-gray-200">{{ $product->supplier->category ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
