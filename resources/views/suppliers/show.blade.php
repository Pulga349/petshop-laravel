@extends('layouts.app')

@section('header')
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-accent">Proveedor</p>
            <h2>Detalle de Proveedor: {{ $supplier->name }}</h2>
        </div>
        <a href="{{ route('suppliers.index') }}" class="btn-secondary">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Supplier Info -->
        <div class="surface p-6">
            <div class="grid grid-cols-2 gap-6 md:grid-cols-3">
                <div>
                    <p class="text-sm text-neutral-400">Nombre</p>
                    <p class="text-lg text-white">{{ $supplier->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Contacto</p>
                    <p class="text-lg text-white">{{ $supplier->contact_person ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Categoría</p>
                    <p class="text-lg text-white">{{ $supplier->category_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Email</p>
                    <p class="text-lg text-white">{{ $supplier->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Teléfono</p>
                    <p class="font-mono text-lg text-white">{{ $supplier->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Dirección</p>
                    <p class="text-lg text-white">{{ $supplier->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div>
            <div class="mb-3">
                <h3 class="text-lg font-semibold text-white">Productos Asociados</h3>
            </div>
            <div class="table-shell">
                <div class="overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>SKU</th>
                                <th>Precio Venta</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplier->products as $product)
                                <tr>
                                    <td class="font-medium text-white">{{ $product->name }}</td>
                                    <td class="font-mono text-white">{{ $product->sku ?? '-' }}</td>
                                    <td class="font-mono font-semibold text-white">${{ number_format($product->sale_price, 2) }}</td>
                                    <td class="font-mono text-white">{{ $product->getStock() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-neutral-400">No hay productos asociados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
