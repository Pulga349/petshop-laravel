@extends('layouts.app')

@section('header')
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-accent">Producto</p>
            <h2>Detalle de Producto: {{ $product->name }}</h2>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Product Info -->
        <div class="surface p-6">
            <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                <div>
                    <p class="text-sm text-neutral-400">Nombre</p>
                    <p class="text-lg text-white">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">SKU</p>
                    <p class="font-mono text-lg text-white">{{ $product->sku ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Categoría</p>
                    <p class="text-lg text-white">{{ $product->category_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Stock</p>
                    <p class="font-mono text-lg text-white">{{ $product->getStock() }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Precio de Venta</p>
                    <p class="font-mono text-lg font-semibold text-white">${{ number_format($product->sale_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Precio de Compra</p>
                    <p class="font-mono text-lg text-white">${{ number_format($product->purchase_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Proveedor</p>
                    <p class="text-lg text-white">{{ $product->supplier->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Descripción</p>
                    <p class="text-lg text-white">{{ $product->description ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Supplier Detail -->
        @if($product->supplier)
        <div class="surface p-6">
            <h3 class="mb-4 text-lg font-semibold text-white">Información del Proveedor</h3>
            <div class="grid grid-cols-2 gap-6 md:grid-cols-3">
                <div>
                    <p class="text-sm text-neutral-400">Nombre</p>
                    <p class="text-lg text-white">{{ $product->supplier->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Contacto</p>
                    <p class="text-lg text-white">{{ $product->supplier->contact_person ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Email</p>
                    <p class="text-lg text-white">{{ $product->supplier->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Teléfono</p>
                    <p class="font-mono text-lg text-white">{{ $product->supplier->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Dirección</p>
                    <p class="text-lg text-white">{{ $product->supplier->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Categoría</p>
                    <p class="text-lg text-white">{{ $product->supplier->category_name ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
