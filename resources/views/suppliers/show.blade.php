@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-200">Detalle de Proveedor: {{ $supplier->name }}</h2>
        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Supplier Info -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-400">Nombre</p>
                    <p class="text-lg text-gray-200">{{ $supplier->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Contacto</p>
                    <p class="text-lg text-gray-200">{{ $supplier->contact_person ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Categoría</p>
                    <p class="text-lg text-gray-200">{{ $supplier->category ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Email</p>
                    <p class="text-lg text-gray-200">{{ $supplier->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Teléfono</p>
                    <p class="text-lg text-gray-200">{{ $supplier->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Dirección</p>
                    <p class="text-lg text-gray-200">{{ $supplier->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-gray-200">Productos Asociados</h3>
            </div>
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Precio Venta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($supplier->products as $product)
                        <tr class="hover:bg-gray-750">
                            <td class="px-6 py-4 text-sm text-gray-200">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-200">{{ $product->sku ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-200">${{ number_format($product->sale_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-200">{{ $product->getStock() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay productos asociados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
