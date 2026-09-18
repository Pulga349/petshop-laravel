@extends('layouts.app')

@section('content')
    <!-- Fila 1: KPIs (Glassmorphism) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Inversión Total -->
        <div class="relative group">
            <div class="absolute inset-0 bg-blue-500/10 rounded-[2rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative bg-[#1a1c23]/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-6 shadow-2xl overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class="bi bi-wallet2 text-7xl text-blue-400"></i>
                </div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-blue-500/10 rounded-2xl">
                        <i class="bi bi-wallet2 text-blue-500 text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Inversión Total</span>
                </div>
                <h3 class="text-3xl font-black text-white tracking-tight">${{ number_format($totalSpent, 2) }}</h3>
                <div class="mt-2">
                    <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Inversión acumulada</span>
                </div>
            </div>
        </div>

        <!-- Ventas Totales -->
        <div class="relative group">
            <div class="absolute inset-0 bg-blue-500/10 rounded-[2rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative bg-[#1a1c23]/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-6 shadow-2xl overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class="bi bi-cart3 text-7xl text-gray-400"></i>
                </div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-white/5 rounded-2xl">
                        <i class="bi bi-cart3 text-gray-400 text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Ventas Totales</span>
                </div>
                <h3 class="text-3xl font-black text-white tracking-tight">${{ number_format($totalSold, 2) }}</h3>
                <div class="mt-2">
                    <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Ingresos generados</span>
                </div>
            </div>
        </div>

        <!-- Utilidad Neta -->
        <div class="relative group">
            <div class="absolute inset-0 bg-emerald-500/10 rounded-[2rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative bg-[#1a1c23]/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-6 shadow-2xl overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class="bi bi-graph-up-arrow text-7xl text-emerald-400"></i>
                </div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-emerald-500/10 rounded-2xl">
                        <i class="bi bi-graph-up-arrow text-emerald-500 text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Utilidad Neta</span>
                </div>
                <h3 class="text-3xl font-black {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tight">
                    ${{ number_format($totalProfit, 2) }}
                </h3>
                <div class="mt-2">
                    <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Margen de ganancia</span>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="relative group">
            <div class="absolute inset-0 bg-red-500/10 rounded-[2rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative bg-[#1a1c23]/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-6 shadow-2xl overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class="bi bi-exclamation-triangle text-7xl text-red-400"></i>
                </div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-red-500/10 rounded-2xl">
                        <i class="bi bi-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Stock Bajo</span>
                </div>
                <h3 class="text-3xl font-black text-white tracking-tight">{{ $lowStockProducts }} ítems</h3>
                <div class="mt-2">
                    <span class="text-[10px] font-bold text-red-500/60 uppercase tracking-widest italic">Atención inmediata</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Gráfico de tendencias -->
    <div class="mb-8">
        <div x-data="salesChart()" class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-xl h-[400px] flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-white tracking-tight">Tendencia de ventas y compras</h3>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Últimos 12 meses</p>
                </div>
                <div class="p-2 bg-emerald-500/10 rounded-lg">
                    <i class="bi bi-activity text-emerald-400"></i>
                </div>
            </div>
            <div class="flex-1 min-h-0 relative">
                <canvas id="monthlyTrendChart"
                        x-ref="salesChart"
                        data-labels='@json($months)'
                        data-sales='@json($salesData)'
                        data-purchases='@json($purchasesData)'></canvas>
            </div>
        </div>
    </div>

    <!-- Fila 3: Tablas Recientes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Sales Table -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white tracking-tight">Ventas Recientes</h3>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Últimas transacciones realizadas</p>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-widest">Ver Todo</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Cliente</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Ítems</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Monto</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($recentSales as $sale)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-xs font-bold text-emerald-400 border border-emerald-500/20">
                                            {{ substr($sale->client->name ?? 'S', 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-200">{{ $sale->client->name ?? 'Consumidor Final' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-medium text-gray-400">{{ $sale->details->count() }} prod.</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-emerald-400">${{ number_format($sale->total, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-blue-500/20">
                                        Completada
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm italic">
                                    No hay ventas registradas recientemente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Purchases Table -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white tracking-tight">Compras Recientes</h3>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Últimos abastecimientos de stock</p>
                </div>
                <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-widest">Ver Todo</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Proveedor</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Ítems</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Costo</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($recentPurchases as $purchase)
                            <?php $purchaseTotal = $purchase->details->sum('subtotal'); ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-xs font-bold text-blue-400 border border-blue-500/20">
                                            {{ substr($purchase->supplier->name ?? 'P', 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-200">{{ $purchase->supplier->name ?? 'Proveedor' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-medium text-gray-400">{{ $purchase->details->count() }} prod.</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-gray-300">${{ number_format($purchaseTotal, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                                        Recibida
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm italic">
                                    No hay compras registradas recientemente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
