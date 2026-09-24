@extends('layouts.app')

@section('header')
    <div class="page-heading"><div><h1>Ventas</h1><p>Monitorea tus ingresos y transacciones de clientes.</p></div><a href="{{ route('pos') }}" class="btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i>Registrar venta</a></div>
@endsection

@section('content')
<div class="space-y-5"><x-list-toolbar :action="route('pos')" action-label="Registrar venta" :search="$search" placeholder="Buscar por cliente o ID..." :sort="$sort" :direction="$direction" :sort-options="['date' => 'Fecha', 'total' => 'Total', 'created_at' => 'Más recientes']" /><div class="table-shell"><div class="overflow-x-auto"><table><thead><tr><th>ID transacción</th><th>Cliente</th><th>Fecha</th><th>Ingreso</th><th class="text-right">Acciones</th></tr></thead><tbody>
    @forelse($sales as $sale)
        <tr><td><span class="font-mono text-xs text-neutral-400">#VTA-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</span></td><td><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center border border-line bg-surface-raised font-bold text-neutral-300">{{ substr($sale->client->name ?? 'C', 0, 1) }}</span><span class="font-semibold text-white">{{ $sale->client->name ?? 'Consumidor Final' }}</span></div></td><td class="text-neutral-400">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td><td class="font-mono font-semibold text-white">${{ number_format($sale->total ?? 0, 2) }}</td><td><div class="flex justify-end gap-1"><a href="{{ route('sales.show', $sale) }}" class="icon-button" aria-label="Ver venta {{ $sale->id }}"><i class="bi bi-eye"></i></a><a href="{{ route('sales.edit', $sale) }}" class="icon-button" aria-label="Editar venta {{ $sale->id }}"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('¿Eliminar esta venta?')">@csrf @method('DELETE')<button class="icon-button border border-danger bg-danger/10 text-white hover:border-accent" aria-label="Eliminar venta {{ $sale->id }}"><i class="bi bi-trash"></i></button></form></div></td></tr>
    @empty
        <tr><td colspan="5" class="py-14 text-center"><i class="bi bi-cash-stack mb-3 block text-3xl text-neutral-400" aria-hidden="true"></i><p class="font-semibold text-white">No hay ventas registradas</p><p class="mt-1 text-sm text-neutral-400">Registra tu primera venta para ver resultados aquí.</p><a href="{{ route('pos') }}" class="btn-primary mt-4">Registrar venta</a></td></tr>
    @endforelse
</tbody></table></div><x-pagination :paginator="$sales" /></div></div>
@endsection
