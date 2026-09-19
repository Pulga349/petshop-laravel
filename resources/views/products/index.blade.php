@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-10">
    <div>
        <p class="text-gray-500 text-sm font-medium italic">Gestione y supervise su inventario</p>
    </div>
    <div class="flex items-center gap-4">
        <button class="flex items-center gap-2 px-5 py-3 bg-[#1a1c23] hover:bg-[#252833] text-gray-300 text-sm font-bold rounded-2xl transition-all border border-white/5 shadow-xl">
            <i class="bi bi-filter"></i>
            Filtros
        </button>
        <a href="{{ route('products.create') }}" class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-black rounded-2xl transition-all shadow-[0_8px_24px_rgba(37,99,235,0.25)] hover:-translate-y-0.5">
            <i class="bi bi-plus-lg"></i>
            Añadir Producto
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <!-- Total Products -->
    <div class="bg-[#1a1c23] border border-white/5 rounded-3xl p-6 shadow-2xl relative overflow-hidden group hover:border-blue-500/30 transition-colors">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-blue-500/10 rounded-2xl">
                <i class="bi bi-box-seam text-blue-500 text-2xl"></i>
            </div>
            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black rounded-lg">
                <i class="bi bi-graph-up"></i>
                Activo
            </div>
        </div>
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Total Productos</p>
        <h3 class="text-3xl font-black text-white tracking-tight">{{ number_format($totalProducts) }}</h3>
        <div class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-blue-500/5 to-transparent"></div>
    </div>

    <!-- Out of Stock -->
    <div class="bg-[#1a1c23] border border-white/5 rounded-3xl p-6 shadow-2xl relative overflow-hidden group hover:border-red-500/30 transition-colors">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-red-500/10 rounded-2xl">
                <i class="bi bi-exclamation-octagon text-red-500 text-2xl {{ $outOfStockCount > 0 ? 'animate-pulse' : '' }}"></i>
            </div>
            <div class="px-2.5 py-1 bg-red-500/10 text-red-500 text-[10px] font-black rounded-lg">
                {{ $outOfStockCount > 0 ? 'URGENTE' : 'ESTABLE' }}
            </div>
        </div>
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Sin Stock</p>
        <h3 class="text-3xl font-black {{ $outOfStockCount > 0 ? 'text-red-500' : 'text-white' }} tracking-tight">{{ $outOfStockCount }}</h3>
        <div class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-red-500/5 to-transparent"></div>
    </div>

    <!-- Total Inventory Value -->
    <div class="bg-[#1a1c23] border border-white/5 rounded-3xl p-6 shadow-2xl relative overflow-hidden group hover:border-blue-500/30 transition-colors">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-blue-500/10 rounded-2xl">
                <i class="bi bi-coin text-blue-500 text-2xl"></i>
            </div>
            <div class="px-2.5 py-1 bg-white/5 text-gray-400 text-[10px] font-black rounded-lg uppercase tracking-widest">
                USD
            </div>
        </div>
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Valor del Inventario</p>
        <h3 class="text-3xl font-black text-white tracking-tight">${{ number_format($totalInventoryValue, 2) }}</h3>
        <div class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-blue-500/5 to-transparent"></div>
    </div>
</div>

<div class="bg-[#1a1c23]/60 backdrop-blur-xl border border-white/5 rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.5)] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/[0.02]">
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Info Producto</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Categoría</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">P. Compra</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">P. Venta</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Estado Stock</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($products as $product)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-500 group-hover:border-blue-500/50 transition-colors overflow-hidden shadow-inner">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="bi bi-image text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-200 leading-none mb-1.5">{{ $product->name }}</h4>
                                <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">SKU: {{ $product->sku ?? 'SIN-SKU' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1.5 bg-white/5 border border-white/5 text-[10px] font-black text-gray-400 rounded-lg uppercase tracking-widest">
                            {{ $product->category_name ?? 'General' }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-sm font-bold text-gray-400">${{ number_format($product->purchase_price, 2) }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-sm font-black text-white">${{ number_format($product->sale_price, 2) }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-2.5">
                            @php
                                $stock = $product->getStock();
                                $status = $stock > 10 ? 'Con Stock' : ($stock > 0 ? 'Stock Bajo' : 'Agotado');
                                $statusColor = match($status) {
                                    'Con Stock' => 'bg-emerald-500',
                                    'Stock Bajo' => 'bg-orange-500',
                                    'Agotado' => 'bg-red-500',
                                    default => 'bg-gray-500'
                                };
                                $textColor = match($status) {
                                    'Con Stock' => 'text-emerald-400',
                                    'Stock Bajo' => 'text-orange-400',
                                    'Agotado' => 'text-red-400',
                                    default => 'text-gray-400'
                                };
                            @endphp
                            <div class="w-2 h-2 rounded-full {{ $statusColor }} shadow-[0_0_8px_rgba(0,0,0,0.5)]"></div>
                            <span class="text-xs font-bold {{ $textColor }}">
                                {{ $status }} ({{ $stock }})
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/5 text-gray-500 hover:text-white transition-all">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 rounded-xl bg-[#2a2b32] border border-white/10 shadow-2xl z-50 py-2 text-left origin-top-right"
                                 style="display: none;">
                                <a href="{{ route('products.edit', $product) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-blue-400 transition-colors">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-white/5 w-full text-left transition-colors" onclick="return confirm('¿Seguro?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-gray-500 text-sm italic">
                        No se encontraron productos en la base de datos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-xs font-bold text-gray-400">
            Página <span class="text-white">{{ $products->currentPage() }}</span> de <span class="text-white">{{ $products->lastPage() }}</span>
        </p>
        <div class="flex items-center gap-2">
            @if($products->onFirstPage())
                <span class="px-3 py-2 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="px-3 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all"><i class="bi bi-chevron-left"></i></a>
            @endif
            
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="px-3 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="px-3 py-2 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
</div>
@endsection
