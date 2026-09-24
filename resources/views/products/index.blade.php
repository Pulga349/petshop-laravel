@extends('layouts.app')

@section('header')
    <div class="page-heading">
        <div><h1>Productos</h1><p>Controla el inventario y detecta faltantes a tiempo.</p></div>
        <span class="status-badge border border-line bg-surface-raised text-white"><i class="bi bi-box-seam mr-2" aria-hidden="true"></i>{{ number_format($totalProducts) }} productos</span>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <article class="surface p-5"><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Total productos</p><p class="mt-2 font-mono text-2xl font-bold text-white">{{ number_format($totalProducts) }}</p><p class="mt-1 text-xs text-neutral-400">Catálogo registrado</p></article>
        <article class="surface p-5"><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Sin stock</p><p class="mt-2 font-mono text-2xl font-bold text-white">{{ $outOfStockCount }}</p><p class="mt-1 text-xs text-neutral-400">Requieren reposición</p></article>
        <article class="surface p-5"><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Valor inventario</p><p class="mt-2 font-mono text-2xl font-bold text-white">${{ number_format($totalInventoryValue, 2) }}</p><p class="mt-1 text-xs text-neutral-400">Costo estimado actual</p></article>
    </div>

    <x-list-toolbar :action="route('products.create')" action-label="Añadir producto" :search="$search" placeholder="Buscar por nombre o SKU..." :sort="$sort" :direction="$direction" :sort-options="['name' => 'Nombre', 'sale_price' => 'Precio de venta', 'purchase_price' => 'Precio de compra', 'created_at' => 'Más recientes']" :filters="[['name' => 'category', 'label' => 'Categoría', 'placeholder' => 'Todas las categorías', 'value' => $category, 'options' => $categories->pluck('name', 'id')->all()]]" />

    <div class="table-shell">
        <div class="overflow-x-auto"><table><thead><tr><th>Producto</th><th>Categoría</th><th>Precios</th><th>Stock</th><th class="text-right">Acciones</th></tr></thead><tbody>
            @forelse($products as $product)
                @php
                    $stock = (int) $product->stock;
                    $status = $stock > 10 ? 'Con stock' : ($stock > 0 ? 'Stock bajo' : 'Agotado');
                    $statusClass = $stock > 10 ? 'border border-success/30 bg-success/10 text-white' : ($stock > 0 ? 'border border-warning/30 bg-warning/10 text-white' : 'border border-danger/30 bg-danger/10 text-white');
                    $statusDot = $stock > 10 ? 'bg-success' : ($stock > 0 ? 'bg-warning' : 'bg-danger');
                @endphp
                <tr>
                    <td><div class="flex items-center gap-3"><div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden border border-line bg-surface-raised text-neutral-400">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="" class="h-full w-full object-cover">@else<i class="bi bi-image" aria-hidden="true"></i>@endif</div><div><p class="font-semibold text-white">{{ $product->name }}</p><p class="font-mono text-xs text-neutral-400">SKU: {{ $product->sku ?? 'Sin SKU' }}</p></div></div></td>
                    <td><span class="status-badge border border-line bg-surface-raised text-neutral-300">{{ $product->category_name ?? 'General' }}</span></td>
                    <td><span class="block font-mono text-xs text-neutral-400">Compra ${{ number_format($product->purchase_price, 2) }}</span><span class="font-mono font-semibold text-white">Venta ${{ number_format($product->sale_price, 2) }}</span></td>
                    <td><span class="status-badge {{ $statusClass }}"><span class="mr-1.5 h-1.5 w-1.5 shrink-0 {{ $statusDot }}" aria-hidden="true"></span>{{ $status }} · {{ $stock }}</span></td>
                    <td><div class="flex justify-end gap-1"><a href="{{ route('products.show', $product) }}" class="icon-button" aria-label="Ver {{ $product->name }}"><i class="bi bi-eye"></i></a><a href="{{ route('products.edit', $product) }}" class="icon-button" aria-label="Editar {{ $product->name }}"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')">@csrf @method('DELETE')<button class="icon-button border border-danger bg-danger/10 text-white hover:border-accent" aria-label="Eliminar {{ $product->name }}"><i class="bi bi-trash"></i></button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-14 text-center"><i class="bi bi-box-seam mb-3 block text-3xl text-neutral-400" aria-hidden="true"></i><p class="font-semibold text-white">No encontramos productos</p><p class="mt-1 text-sm text-neutral-400">Prueba otra búsqueda o añade tu primer producto.</p><a href="{{ route('products.create') }}" class="btn-primary mt-4">Añadir producto</a></td></tr>
            @endforelse
        </tbody></table></div>
        <x-pagination :paginator="$products" />
    </div>
</div>
@endsection
