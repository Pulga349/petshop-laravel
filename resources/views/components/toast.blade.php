@props(['type' => 'info', 'title' => null, 'dismissible' => true])

@php
    // Flat monochrome panel; chromatic color only on the icon micro-indicator.
    $icons = [
        'success' => 'bi-check-lg',
        'error'   => 'bi-x-lg',
        'warning' => 'bi-exclamation-triangle-fill',
        'info'    => 'bi-info-lg',
    ];

    $iconColors = [
        'success' => 'text-success',
        'error'   => 'text-danger',
        'warning' => 'text-warning',
        'info'    => 'text-accent',
    ];
@endphp

<div x-data="{ visible: true }"
     x-show="visible"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 translate-x-4"
     class="flex w-full items-start gap-3 border border-line-focus bg-surface-raised p-4"
     role="alert">

    <span class="flex h-8 w-8 shrink-0 items-center justify-center border border-line-focus {{ $iconColors[$type] }}">
        <i class="bi {{ $icons[$type] }} text-base" aria-hidden="true"></i>
    </span>

    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-white">{{ $title ?: $slot }}</p>
        @if($title && trim((string) $slot) !== '')
            <p class="mt-0.5 text-xs text-neutral-400">{{ $slot }}</p>
        @endif
    </div>

    @if($dismissible)
        <button type="button"
                @click="visible = false"
                class="-mr-1 -mt-1 flex h-7 w-7 shrink-0 items-center justify-center text-neutral-400 transition hover:bg-surface-hover hover:text-white"
                aria-label="Cerrar">
            <i class="bi bi-x-lg text-sm" aria-hidden="true"></i>
        </button>
    @endif
</div>
