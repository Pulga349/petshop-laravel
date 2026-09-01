@props(['type' => 'info', 'title' => null, 'dismissible' => true])

@php
    $colors = [
        'success' => 'bg-emerald-500 border-emerald-600',
        'error'   => 'bg-red-500 border-red-600', 
        'warning' => 'bg-yellow-500 border-yellow-600',
        'info'    => 'bg-blue-500 border-blue-600',
    ];
    
    $icons = [
        'success' => 'bi-check-circle',
        'error'   => 'bi-x-circle',
        'warning' => 'bi-exclamation-triangle',
        'info'    => 'bi-info-circle',
    ];
    
    $bgColors = [
        'success' => 'bg-emerald-500/10 border-emerald-500/30',
        'error'   => 'bg-red-500/10 border-red-500/30',
        'warning' => 'bg-yellow-500/10 border-yellow-500/30', 
        'info'    => 'bg-blue-500/10 border-blue-500/30',
    ];
    
    $textColors = [
        'success' => 'text-emerald-400',
        'error'   => 'text-red-400',
        'warning' => 'text-yellow-400',
        'info'    => 'text-blue-400',
    ];
    
    $iconColors = [
        'success' => 'text-emerald-500',
        'error'   => 'text-red-500',
        'warning' => 'text-yellow-500',
        'info'    => 'text-blue-500',
    ];
@endphp

<div x-data="toastItem('{{ $type }}', {{ $dismissible ? 'true' : 'false' }})"
     x-show="visible"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-100 translate-x-full"
     class="relative flex items-start gap-3 p-4 mb-3 rounded-lg border shadow-lg backdrop-blur-sm {{ $bgColors[$type] }} {{ $colors[$type] }}/20 border-l-4"
     role="alert">
    
    <!-- Icono -->
    <i class="bi {{ $icons[$type] }} text-xl {{ $iconColors[$type] }}"></i>
    
    <!-- Content -->
    <div class="flex-1 min-w-0">
        @if($title)
            <h4 class="font-semibold text-gray-100 mb-1">{{ $title }}</h4>
        @endif
        <div class="text-sm text-gray-300">
            {{ $slot }}
        </div>
    </div>
    
    <!-- Close button -->
    @if($dismissible)
        <button @click="dismiss()" 
                class="p-1 text-gray-400 hover:text-white transition rounded hover:bg-white/10"
                aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
        </button>
    @endif
</div>