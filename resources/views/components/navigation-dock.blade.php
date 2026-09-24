<div class="fixed bottom-4 left-1/2 z-[100] flex -translate-x-1/2 flex-col items-center gap-2">
    <div class="flex items-center gap-1 border border-line bg-surface-raised p-2">
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" id="logout-dock-form">
            @csrf
            <button type="submit" data-nav-key="q" class="icon-button group relative text-neutral-400 hover:bg-danger/10 hover:text-white" title="Cerrar sesión (Ctrl + Q)" aria-label="Cerrar sesión">
                <i class="bi bi-box-arrow-right text-xl"></i>
                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 border border-line bg-surface-raised px-2 py-0.5 text-[10px] font-bold text-white opacity-0 transition-opacity group-hover:opacity-100">
                    Q
                </span>
            </button>
        </form>

        <div class="mx-1 h-6 w-px bg-line"></div>

        {{-- Nav Links --}}
        @php
            $links = [
                ['route' => 'dashboard', 'icon' => 'bi-grid-1x2', 'active_icon' => 'bi-grid-1x2-fill', 'label' => 'Inicio', 'key' => '1'],
                ['route' => 'products.index', 'icon' => 'bi-box-seam', 'active_icon' => 'bi-box-seam-fill', 'label' => 'Productos', 'key' => '2'],
                ['route' => 'suppliers.index', 'icon' => 'bi-truck', 'active_icon' => 'bi-truck', 'label' => 'Proveedores', 'key' => '3'],
                ['route' => 'purchases.index', 'icon' => 'bi-cart-check', 'active_icon' => 'bi-cart-check-fill', 'label' => 'Compras', 'key' => '4'],
                ['route' => 'pos', 'icon' => 'bi-shop', 'active_icon' => 'bi-shop', 'label' => 'Punto de Venta', 'key' => '5'],
                ['route' => 'clients.index', 'icon' => 'bi-people', 'active_icon' => 'bi-people-fill', 'label' => 'Clientes', 'key' => '6'],
            ];
        @endphp

        @foreach($links as $link)
            @php
                $isActive = request()->routeIs($link['route']) || (str_contains($link['route'], '.') && request()->routeIs(explode('.', $link['route'])[0] . '.*'));
                $currentIcon = $isActive ? ($link['active_icon'] ?? $link['icon']) : $link['icon'];
            @endphp
            <a href="{{ route($link['route']) }}" 
               data-nav-key="{{ $link['key'] }}"
               class="icon-button group relative {{ $isActive ? 'bg-surface-hover text-white' : 'text-neutral-400 hover:bg-surface-hover hover:text-white' }}"
               title="{{ $link['label'] }}" aria-label="{{ $link['label'] }}">
                <i class="bi {{ $currentIcon }} text-xl"></i>
                
                {{-- Shortcut Pill --}}
                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 border border-line bg-surface-raised px-2 py-0.5 text-[10px] font-bold text-white opacity-0 transition-opacity group-hover:opacity-100">
                    {{ $link['key'] }}
                </span>
            </a>
        @endforeach
    </div>
    
    <div class="hidden text-[10px] font-medium tracking-wide text-neutral-400 sm:block">
        Atajos: <span class="text-white">Ctrl + 1..6</span> para navegar | <span class="text-white">Ctrl + Q</span> para salir
    </div>
</div>
