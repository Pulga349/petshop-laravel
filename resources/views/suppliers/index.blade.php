@extends('layouts.app')

@section('header')
    <div class="page-heading"><div><h1>Proveedores</h1><p>Gestiona socios, contactos y abastecimiento.</p></div><span class="status-badge border border-line bg-surface-raised text-white"><i class="bi bi-truck mr-2" aria-hidden="true"></i>Catálogo de proveedores</span></div>
@endsection

@section('content')
<div class="space-y-6">
    <x-list-toolbar :action="route('suppliers.create')" action-label="Añadir proveedor" :search="$search" placeholder="Buscar por nombre o correo..." :sort="$sort" :direction="$direction" :sort-options="['name' => 'Nombre', 'status' => 'Estado', 'created_at' => 'Más recientes']" :filters="[['name' => 'status', 'label' => 'Estado', 'placeholder' => 'Todos los estados', 'value' => $status, 'options' => ['Active' => 'Activo', 'Inactive' => 'Inactivo']], ['name' => 'category', 'label' => 'Categoría', 'placeholder' => 'Todas las categorías', 'value' => $category, 'options' => array_combine(config('categories.suppliers'), config('categories.suppliers'))]]" />
    <div class="table-shell">
        <div class="overflow-x-auto"><table><thead><tr><th>Proveedor</th><th>Contacto</th><th>Categoría</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead><tbody>
            @forelse($suppliers as $supplier)
                <tr><td><div class="flex items-center gap-3"><div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden border border-line bg-surface-raised font-bold text-neutral-300">@if($supplier->logo)<img src="{{ asset('storage/'.$supplier->logo) }}" alt="" class="h-full w-full object-cover">@else{{ substr($supplier->name, 0, 1) }}@endif</div><div><p class="font-semibold text-white">{{ $supplier->name }}</p><p class="text-xs text-neutral-400">{{ $supplier->email ?? 'Sin correo' }}</p></div></div></td><td><p>{{ $supplier->contact_person ?? 'No asignado' }}</p><p class="text-xs text-neutral-400">{{ $supplier->phone ?? 'Sin teléfono' }}</p></td><td><span class="status-badge border border-line bg-surface-raised text-neutral-300">{{ $supplier->category_name ?? 'Otros' }}</span></td><td>@if($supplier->status === 'Active')<span class="status-badge border border-success/30 bg-success/10 text-white"><span class="mr-1.5 h-1.5 w-1.5 shrink-0 bg-success" aria-hidden="true"></span>Activo</span>@else<span class="status-badge border border-line bg-surface-raised text-neutral-300"><span class="mr-1.5 h-1.5 w-1.5 shrink-0 bg-neutral-500" aria-hidden="true"></span>Inactivo</span>@endif</td><td><div class="flex justify-end gap-1"><a href="{{ route('suppliers.show', $supplier) }}" class="icon-button" aria-label="Ver {{ $supplier->name }}"><i class="bi bi-eye"></i></a><a href="{{ route('suppliers.edit', $supplier) }}" class="icon-button" aria-label="Editar {{ $supplier->name }}"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('¿Eliminar este proveedor?')">@csrf @method('DELETE')<button class="icon-button border border-danger bg-danger/10 text-white hover:border-accent" aria-label="Eliminar {{ $supplier->name }}"><i class="bi bi-trash"></i></button></form></div></td></tr>
            @empty
                <tr><td colspan="5" class="py-14 text-center"><i class="bi bi-truck mb-3 block text-3xl text-neutral-400" aria-hidden="true"></i><p class="font-semibold text-white">No hay proveedores registrados</p><p class="mt-1 text-sm text-neutral-400">Añade un proveedor para organizar tus compras.</p><a href="{{ route('suppliers.create') }}" class="btn-primary mt-4">Añadir proveedor</a></td></tr>
            @endforelse
        </tbody></table></div>
        <x-pagination :paginator="$suppliers" />
    </div>
</div>
@endsection
