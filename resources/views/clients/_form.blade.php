@php($editing = $client !== null)
<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex items-start justify-between gap-4"><div><p class="text-sm font-medium text-accent">Clientes</p><h1 class="mt-1 text-2xl font-bold text-white">{{ $editing ? 'Editar cliente' : 'Nuevo cliente' }}</h1><p class="mt-1 text-sm text-neutral-400">Guarda los datos esenciales para sus próximas compras.</p></div><a href="{{ route('clients.index') }}" class="icon-button" aria-label="Cerrar formulario"><i class="bi bi-x-lg"></i></a></div>
    <form action="{{ $action }}" method="POST" class="form-panel">
        @csrf @if($editing) @method('PUT') @endif
        <section class="form-section"><h2 class="text-lg font-semibold text-white">Datos personales</h2><p>El nombre es obligatorio; el resto ayuda a mantener una comunicación clara.</p><div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2"><label for="name" class="field-label">Nombre completo <span class="required">*</span></label><input id="name" name="name" value="{{ old('name', $client?->name) }}" required class="field-control">@error('name')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="email" class="field-label">Correo electrónico</label><input id="email" type="email" name="email" value="{{ old('email', $client?->email) }}" class="field-control">@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="phone" class="field-label">Teléfono</label><input id="phone" name="phone" value="{{ old('phone', $client?->phone) }}" class="field-control">@error('phone')<p class="field-error">{{ $message }}</p>@enderror</div>
        </div></section>
        <section class="form-section"><h2 class="text-lg font-semibold text-white">Ubicación</h2><p>Opcional: registra una dirección para futuras entregas.</p><div class="mt-5"><label for="address" class="field-label">Dirección</label><textarea id="address" name="address" rows="4" class="field-control py-3">{{ old('address', $client?->address) }}</textarea>@error('address')<p class="field-error">{{ $message }}</p>@enderror</div></section>
        <div class="form-actions"><a href="{{ route('clients.index') }}" class="btn-secondary">Cancelar</a><button type="submit" class="btn-primary">{{ $editing ? 'Guardar cambios' : 'Guardar cliente' }}</button></div>
    </form>
</div>
