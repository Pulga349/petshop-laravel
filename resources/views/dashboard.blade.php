@extends('layouts.app')

@section('content')
@php
    $userName = Auth::user()->name ?? 'Usuario';
    $salesVariation = $kpiComparisons['sales'] ?? 0;
    $purchasesVariation = $kpiComparisons['purchases'] ?? 0;
    $profitVariation = $kpiComparisons['profit'] ?? 0;
    $trend = static function (float|int $variation): string {
        return $variation > 0 ? 'up' : ($variation < 0 ? 'down' : 'flat');
    };
@endphp

<div class="space-y-8">
    <section class="page-heading" aria-labelledby="dashboard-heading">
        <div>
            <p class="mb-1 text-sm font-medium text-neutral-400">{{ now()->isoFormat('dddd, D [de] MMMM') }}</p>
            <h1 id="dashboard-heading">Buenos días, {{ $userName }}</h1>
            <p>Una vista clara de las operaciones de tu tienda.</p>
        </div>
    </section>

    <section aria-labelledby="kpi-heading">
        <div class="mb-3 flex items-center justify-between">
            <div>
                <h2 id="kpi-heading" class="text-lg font-semibold text-white">Resumen del periodo</h2>
                <p class="text-sm text-neutral-400">Comparado con el mes anterior</p>
            </div>
            <span class="hidden text-xs text-neutral-400 sm:inline">{{ now()->translatedFormat('F Y') }}</span>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['label' => 'Ventas del mes', 'value' => '$'.number_format($salesThisMonth, 2), 'variation' => $salesVariation, 'key' => 'sales', 'icon' => 'bi-cash-stack', 'data' => $salesData],
                ['label' => 'Compras del mes', 'value' => '$'.number_format($purchasesThisMonth, 2), 'variation' => $purchasesVariation, 'key' => 'purchases', 'icon' => 'bi-cart-check', 'data' => $purchasesData],
                ['label' => 'Utilidad del mes', 'value' => '$'.number_format($profitThisMonth, 2), 'variation' => $profitVariation, 'key' => 'profit', 'icon' => 'bi-graph-up-arrow', 'data' => $salesData],
            ] as $kpi)
                <article class="surface p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">{{ $kpi['label'] }}</p>
                            <p class="mt-2 font-mono text-2xl font-bold text-white">{{ $kpi['value'] }}</p>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center border border-line bg-surface-raised text-neutral-300"><i class="bi {{ $kpi['icon'] }}" aria-hidden="true"></i></span>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-3">
                        <span class="status-badge {{ $kpi['variation'] >= 0 ? 'border border-success/30 bg-success/10 text-white' : 'border border-danger/30 bg-danger/10 text-white' }}">
                            <i class="bi bi-arrow-{{ $kpi['variation'] >= 0 ? 'up' : 'down' }}-short {{ $kpi['variation'] >= 0 ? 'text-success' : 'text-danger' }}" aria-hidden="true"></i>{{ abs($kpi['variation']) }}%
                        </span>
                        <span class="text-xs text-neutral-400">vs. mes anterior</span>
                        <span class="flex items-end gap-0.5" aria-label="Tendencia {{ $trend($kpi['variation']) }}">
                            @foreach(array_slice($kpi['data'], -6) as $point)
                                <span class="w-1.5 bg-neutral-400" style="height: {{ max(4, min(22, ((float) $point / max((float) max($kpi['data']), 1)) * 22)) }}px"></span>
                            @endforeach
                        </span>
                    </div>
                </article>
            @endforeach

            <article class="surface p-5">
                <div class="flex items-start justify-between gap-3">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Stock bajo</p><p class="mt-2 text-2xl font-bold text-white"><span class="font-mono">{{ $lowStockProducts }}</span> ítems</p></div>
                    <span class="flex h-10 w-10 items-center justify-center border border-warning/30 bg-warning/10 text-warning"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
                </div>
                <p class="mt-4 text-xs text-neutral-400">{{ $totalProducts }} productos · {{ $totalClients }} clientes activos</p>
            </article>
        </div>
    </section>

    <section class="surface p-5 sm:p-6" x-data="salesChart()" aria-labelledby="trend-heading">
        <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
            <div><h2 id="trend-heading" class="text-lg font-semibold text-white">Tendencia de ventas y compras</h2><p x-ref="rangeLabel" class="text-sm text-neutral-400">{{ $rangeLabel }}</p></div>
            <div class="flex items-center gap-3">
                <label for="dashboard-range" class="sr-only">Rango del gráfico</label>
                <select id="dashboard-range" x-ref="rangeSelect" name="range" class="field-control w-auto min-w-40" aria-label="Rango del gráfico" @change="setRange($event.target.value)">
                    @foreach(['1m' => 'Este mes', '3m' => 'Últimos 3 meses', '12m' => 'Últimos 12 meses'] as $rangeValue => $rangeText)
                        <option value="{{ $rangeValue }}" @selected($range === $rangeValue)>{{ $rangeText }}</option>
                    @endforeach
                </select>
                <span class="status-badge border border-line bg-surface-raised text-white">Resumen</span>
            </div>
        </div>
        <div class="h-72"><canvas id="monthlyTrendChart" x-ref="salesChart" data-labels='@json($months)' data-sales='@json($salesData)' data-purchases='@json($purchasesData)' data-range="{{ $range }}" data-url="{{ url()->route('dashboard.chart-data', [], false) }}" aria-label="Gráfico de ventas y compras: {{ $rangeLabel }}"></canvas></div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-2" aria-label="Transacciones recientes">
        @foreach([['title' => 'Ventas recientes', 'subtitle' => 'Últimas transacciones realizadas', 'route' => 'sales.index', 'items' => $recentSales, 'person' => 'client', 'fallback' => 'Consumidor Final', 'amount' => 'total', 'empty' => 'No hay ventas registradas recientemente.'], ['title' => 'Compras recientes', 'subtitle' => 'Últimos abastecimientos de stock', 'route' => 'purchases.index', 'items' => $recentPurchases, 'person' => 'supplier', 'fallback' => 'Proveedor', 'amount' => null, 'empty' => 'No hay compras registradas recientemente.']] as $table)
            <div class="table-shell">
                <div class="flex items-center justify-between gap-3 p-5"><div><h2 class="text-base font-semibold text-white">{{ $table['title'] }}</h2><p class="text-xs text-neutral-400">{{ $table['subtitle'] }}</p></div><a href="{{ route($table['route']) }}" class="text-xs font-semibold text-accent underline hover:text-white">Ver todo</a></div>
                <div class="overflow-x-auto"><table><thead><tr><th>Persona</th><th>Ítems</th><th class="text-right">Monto</th></tr></thead><tbody>
                    @forelse($table['items'] as $item)
                        @php $person = optional($item->{$table['person']})->name ?? $table['fallback']; $amount = $table['amount'] ? $item->{$table['amount']} : $item->details->sum('subtotal'); @endphp
                        <tr><td><span class="font-medium">{{ $person }}</span></td><td class="font-mono text-neutral-400">{{ $item->details->count() }}</td><td class="text-right font-mono font-semibold text-white">${{ number_format($amount, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="py-10 text-center text-sm text-neutral-400">{{ $table['empty'] }}</td></tr>
                    @endforelse
                </tbody></table></div>
            </div>
        @endforeach
    </section>
</div>
@endsection
