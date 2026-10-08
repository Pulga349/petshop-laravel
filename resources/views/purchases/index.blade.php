@extends('layouts.app')

@section('header')
    <div class="page-heading"><div><h1>Compras</h1><p>Sigue el historial de abastecimiento y reposición.</p></div><a href="{{ route('purchases.create') }}" class="btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i>Registrar compra</a></div>
@endsection

@section('content')
<div class="space-y-5"><x-list-toolbar :action="route('purchases.create')" action-label="Registrar compra" :search="$search" placeholder="Buscar por proveedor o ID..." :sort="$sort" :direction="$direction" :sort-options="['date' => 'Fecha', 'created_at' => 'Más recientes']" /><div class="table-shell"><div class="overflow-x-auto"><table><thead><tr><th>ID transacción</th><th>Proveedor</th><th>Fecha</th><th>Total</th><th class="text-right">Acciones</th></tr></thead><tbody>
    @forelse($purchases as $purchase)
        <tr><td><span class="font-mono text-xs text-neutral-400">#COM-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</span></td><td><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center border border-line bg-surface-raised font-bold text-neutral-300">{{ substr($purchase->supplier->name ?? 'P', 0, 1) }}</span><span class="font-semibold text-white">{{ $purchase->supplier->name ?? 'Sin proveedor' }}</span></div></td><td class="text-neutral-400">{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</td><td class="font-mono font-semibold text-white">${{ number_format($purchase->total_computed ?? 0, 2) }}</td><td><div class="flex justify-end gap-1"><a href="{{ route('purchases.show', $purchase) }}" class="icon-button" aria-label="Ver compra {{ $purchase->id }}"><i class="bi bi-eye"></i></a><a href="{{ route('purchases.edit', $purchase) }}" class="icon-button" aria-label="Editar compra {{ $purchase->id }}"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('purchases.destroy', $purchase) }}" onsubmit="return confirm('¿Eliminar esta compra?')">@csrf @method('DELETE')<button class="icon-button border border-danger bg-danger/10 text-white hover:border-accent" aria-label="Eliminar compra {{ $purchase->id }}"><i class="bi bi-trash"></i></button></form></div></td></tr>
    @empty
        <tr><td colspan="5" class="py-14 text-center"><i class="bi bi-cart-check mb-3 block text-3xl text-neutral-400" aria-hidden="true"></i><p class="font-semibold text-white">No hay compras registradas</p><p class="mt-1 text-sm text-neutral-400">Registra una compra para actualizar tu inventario.</p><a href="{{ route('purchases.create') }}" class="btn-primary mt-4">Registrar compra</a></td></tr>
    @endforelse
</tbody></table></div><x-pagination :paginator="$purchases" /></div></div>
@endsection
