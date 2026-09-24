@extends('layouts.app')

@php
    $neto = (float) $sale->total + (float) $sale->discount_amount;
    $descuento = (float) $sale->discount_amount;
    $descuentoPct = ($neto > 0 && $descuento > 0) ? round($descuento / $neto * 100, 1) : null;
    $ivaRate = (float) \App\Models\Setting::get('iva_rate', 21);
    $iva = (float) $sale->iva_amount;
    $paymentLabels = config('payment_methods', []);
    $metodoPago = $paymentLabels[$sale->payment_method] ?? 'No registrado';
    $cobrada = $sale->payment_method !== null;
    $vuelto = ($sale->payment_method === 'efectivo' && $sale->amount_paid !== null)
        ? max(0, (float) $sale->amount_paid - (float) $sale->total)
        : null;
@endphp

@section('header')
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-accent">Comprobante de venta</p>
            <h2 class="font-mono">#VTA-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <p>
                Fecha <span class="font-mono text-white">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</span>
                &middot; Estado
                <span class="status-badge ml-1 border border-line bg-surface-raised text-white">
                    <span class="mr-1.5 h-1.5 w-1.5 {{ $cobrada ? 'bg-success' : 'bg-warning' }}" aria-hidden="true"></span>
                    {{ $cobrada ? 'Cobrada' : 'Pendiente de pago' }}
                </span>
            </p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn-secondary">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Client Block -->
        <div class="surface p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <p class="text-sm text-neutral-400">Cliente</p>
                    <p class="text-lg font-semibold text-white">{{ $sale->client->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Nivel</p>
                    <p class="mt-1">
                        <span class="status-badge border border-line bg-surface-raised text-white">
                            <span class="mr-1.5 h-1.5 w-1.5 bg-accent" aria-hidden="true"></span>
                            {{ $sale->client->tier ?? 'Bronze' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Total</p>
                    <p class="font-mono text-lg font-semibold text-white">${{ number_format($sale->total, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-shell">
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-right">Cant</th>
                            <th class="text-right">P. Unit</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sale->details as $detail)
                            <tr>
                                <td class="font-medium text-white">{{ $detail->product->name }}</td>
                                <td class="text-right font-mono text-white">{{ $detail->quantity }}</td>
                                <td class="text-right font-mono text-white">${{ number_format($detail->unit_price, 2) }}</td>
                                <td class="text-right font-mono font-semibold text-white">${{ number_format($detail->subtotal, 2) }}</td>
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

        <!-- Totals + Payment -->
        <div class="surface p-6">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                <!-- Totals -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-400">Totales</h3>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-400">Neto</dt>
                            <dd class="font-mono text-white">${{ number_format($neto, 2) }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-400">Descuento{{ $descuentoPct !== null ? ' (' . $descuentoPct . '%)' : '' }}</dt>
                            <dd class="font-mono text-white">{{ $descuento > 0 ? '-$' . number_format($descuento, 2) : '—' }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-400">IVA {{ $ivaRate + 0 }}% Discriminado</dt>
                            <dd class="font-mono text-white">${{ number_format($iva, 2) }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 border-t border-line pt-3">
                            <dt class="font-semibold text-white">Total</dt>
                            <dd class="font-mono text-lg font-bold text-white">${{ number_format($sale->total, 2) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Payment -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-400">Pago</h3>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-400">Método de pago</dt>
                            <dd class="text-white">{{ $metodoPago }}</dd>
                        </div>
                        @if($sale->amount_paid !== null)
                            <div class="flex items-baseline justify-between gap-4">
                                <dt class="text-neutral-400">Pagado</dt>
                                <dd class="font-mono text-white">${{ number_format($sale->amount_paid, 2) }}</dd>
                            </div>
                        @endif
                        @if($vuelto !== null)
                            <div class="flex items-baseline justify-between gap-4 border-t border-line pt-3">
                                <dt class="font-semibold text-white">Vuelto</dt>
                                <dd class="font-mono text-lg font-bold text-white">${{ number_format($vuelto, 2) }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
