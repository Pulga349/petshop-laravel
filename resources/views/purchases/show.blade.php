@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-200">Detalle de Compra #{{ $purchase->id }}</h2>
        <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Purchase Info -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-400">Fecha</p>
                    <p class="text-lg text-gray-200">{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Proveedor</p>
                    <p class="text-lg text-gray-200">{{ $purchase->supplier->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Total</p>
                    <p class="text-lg text-emerald-400 font-semibold">${{ number_format($purchase->details->sum('subtotal'), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Precio Unit.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($purchase->details as $detail)
                        <tr class="hover:bg-gray-750">
                            <td class="px-6 py-4 text-sm text-gray-200">{{ $detail->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-200">{{ $detail->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-200">${{ number_format($detail->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-200">${{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay detalles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
