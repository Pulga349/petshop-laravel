@extends('layouts.app')

@section('header')
<div class="mb-10">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <!-- Left: Title -->
        <div>
            <h2 class="text-3xl font-black text-white uppercase tracking-tight">Ventas</h2>
            <p class="text-gray-500 text-sm mt-1 font-medium italic">Monitoree sus ingresos y transacciones de clientes</p>
        </div>

        <!-- Right: New Sale Button -->
        <div>
            <a href="{{ route('sales.create') }}" 
               class="group flex items-center gap-3 bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-[20px] text-sm font-black transition-all shadow-[0_8px_20px_rgba(37,99,235,0.3)] hover:-translate-y-0.5 uppercase tracking-widest">
                <i class="bi bi-plus-lg text-lg"></i>
                Registrar Venta
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="bg-[#1a1c23]/60 backdrop-blur-xl border border-white/5 rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.5)] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/[0.02]">
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">ID Transacción</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Info del Cliente</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Fecha de Venta</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Ingreso</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($sales as $sale)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-8 py-6">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
                            #VTA-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 font-bold text-sm">
                                {{ substr($sale->client->name ?? 'C', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-200">{{ $sale->client->name ?? 'Consumidor Final' }}</h4>
                                <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Miembro Premium</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-sm font-medium text-gray-400">
                            {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-sm font-black text-emerald-400">${{ number_format($sale->total ?? 0, 2) }}</span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('sales.show', $sale) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg hover:bg-white/5 text-gray-500 hover:text-blue-400 transition-all">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center text-gray-500 text-sm italic">
                        Aún no hay ventas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-xs font-bold text-gray-400">
            Página <span class="text-white">{{ $sales->currentPage() }}</span> de <span class="text-white">{{ $sales->lastPage() }}</span>
        </p>
        <div class="flex items-center gap-2">
            @if($sales->onFirstPage())
                <span class="px-3 py-2 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $sales->previousPageUrl() }}" class="px-3 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all"><i class="bi bi-chevron-left"></i></a>
            @endif
            
            @if($sales->hasMorePages())
                <a href="{{ $sales->nextPageUrl() }}" class="px-3 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="px-3 py-2 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
</div>
@endsection
