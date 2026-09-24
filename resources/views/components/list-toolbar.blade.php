@props([
    'action' => null,
    'actionLabel' => null,
    'search' => '',
    'placeholder' => 'Buscar...',
    'filters' => [],
    'sort' => 'created_at',
    'direction' => 'desc',
    'sortOptions' => ['created_at' => 'Más recientes', 'name' => 'Nombre', 'date' => 'Fecha'],
])

@php
    // Action link lives OUTSIDE the form (sibling), so it never submits with filters.
    $wrapperClass = $action ? 'mb-5 flex flex-col gap-3 lg:flex-row lg:items-center' : '';
    $formClass = 'surface-muted flex flex-1 flex-col gap-3 p-3 lg:flex-row lg:items-center' . ($action ? '' : ' mb-5');
@endphp

<div class="{{ $wrapperClass }}">
    <form method="GET" class="{{ $formClass }}">
        <label class="relative min-w-0 flex-1">
            <span class="sr-only">{{ $placeholder }}</span>
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500" aria-hidden="true"></i>
            <input type="search" name="search" value="{{ $search }}" placeholder="{{ $placeholder }}" class="field-control pl-10">
        </label>
        @foreach($filters as $filter)
            <label class="min-w-40">
                <span class="sr-only">{{ $filter['label'] }}</span>
                <select name="{{ $filter['name'] }}" class="field-control">
                    <option value="">{{ $filter['placeholder'] }}</option>
                    @foreach($filter['options'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filter['value'] ?? '') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        @endforeach
        <input type="hidden" name="direction" value="{{ $direction }}">
        <label class="min-w-40">
            <span class="sr-only">Ordenar por</span>
            <select name="sort" class="field-control">
                @foreach($sortOptions as $value => $label)
                    <option value="{{ $value }}" @selected($sort == $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="btn-primary">Aplicar</button>
        @if(request()->query())
            <a href="{{ url()->current() }}" class="btn-secondary">Limpiar</a>
        @endif
    </form>
    @if($action)
        <a href="{{ $action }}" class="btn-primary whitespace-nowrap"><i class="bi bi-plus-lg" aria-hidden="true"></i>{{ $actionLabel }}</a>
    @endif
</div>
