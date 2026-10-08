@extends('layouts.app')

@section('header')
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-accent">Compra</p>
            <h2>Detalle de Compra #<span class="font-mono">{{ $purchase->id }}</span></h2>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn-secondary">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Purchase Info -->
        <div class="surface p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <p class="text-sm text-neutral-400">Fecha</p>
                    <p class="font-mono text-lg text-white">{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Proveedor</p>
                    <p class="text-lg text-white">{{ $purchase->supplier->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Total</p>
                    <p class="font-mono text-lg font-semibold text-white">${{ number_format($purchase->details->sum('subtotal'), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="table-shell">
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->details as $detail)
                            <tr>
                                <td class="font-medium text-white">{{ $detail->product->name }}</td>
                                <td class="font-mono text-white">{{ $detail->quantity }}</td>
                                <td class="font-mono text-white">${{ number_format($detail->unit_price, 2) }}</td>
                                <td class="font-mono font-semibold text-white">${{ number_format($detail->subtotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-neutral-400">No hay detalles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
