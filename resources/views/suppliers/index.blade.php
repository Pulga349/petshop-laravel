@extends('layouts.app')

@section('header')
<div class="mb-10">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <!-- Left: Title -->
        <div>
            <h2 class="text-3xl font-black text-white uppercase tracking-tight">Proveedores</h2>
            <p class="text-gray-500 text-sm mt-1 font-medium italic">Gestión de socios globales y logística de abastecimiento</p>
        </div>

        <!-- Right: New Supplier Button -->
        <div>
            <a href="{{ route('suppliers.create') }}" 
               class="group flex items-center gap-3 bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-[20px] text-sm font-black transition-all shadow-[0_8px_20px_rgba(37,99,235,0.3)] hover:-translate-y-0.5 uppercase tracking-widest">
                <i class="bi bi-plus-lg text-lg"></i>
                Añadir Proveedor
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
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Info del Socio</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Contacto Principal</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Categoría</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Estado</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @php
                    $categoryStyles = [
                        'Alimento' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                        'Accesorios' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'Higiene' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                        'Otros' => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                    ];
                @endphp

                @forelse($suppliers as $supplier)
                @php
                    $style = $categoryStyles[$supplier->category] ?? $categoryStyles['Otros'];
                @endphp
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-500 font-black text-xl overflow-hidden shadow-inner">
                                @if($supplier->logo)
                                    <img src="{{ asset('storage/' . $supplier->logo) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($supplier->name, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-200 leading-none mb-1.5">{{ $supplier->name }}</h4>
                                <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">{{ $supplier->email ?? 'sin-email@socio.com' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-300">{{ $supplier->contact_person ?? 'No asignado' }}</span>
                            <span class="text-xs text-gray-500">{{ $supplier->phone ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1.5 {{ $style }} text-[10px] font-black uppercase tracking-widest rounded-lg border">
                            {{ $supplier->category }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $supplier->status === 'Active' ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                            <span class="text-xs font-bold {{ $supplier->status === 'Active' ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $supplier->status === 'Active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/5 text-gray-500 hover:text-white transition-all">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 rounded-xl bg-[#2a2b32] border border-white/10 shadow-2xl z-10 py-2 text-left">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-blue-400">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-white/5 w-full text-left" onclick="return confirm('¿Seguro?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center text-gray-500 text-sm italic">
                        No hay socios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-xs font-bold text-gray-400">
            Página <span class="text-white">{{ $suppliers->currentPage() }}</span> de <span class="text-white">{{ $suppliers->lastPage() }}</span>
        </p>
        <div class="flex items-center gap-2">
            @if($suppliers->onFirstPage())
                <span class="px-3 py-2 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $suppliers->previousPageUrl() }}" class="px-3 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all"><i class="bi bi-chevron-left"></i></a>
            @endif
            
            @if($suppliers->hasMorePages())
                <a href="{{ $suppliers->nextPageUrl() }}" class="px-3 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="px-3 py-2 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
</div>
@endsection
