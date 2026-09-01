<div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] flex flex-col items-center gap-3">
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-2 flex items-center gap-1 shadow-2xl">
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" id="logout-dock-form">
            @csrf
            <button type="submit" data-nav-key="q" class="p-3 rounded-xl hover:bg-white/10 transition-all text-gray-400 hover:text-red-400 group relative flex items-center justify-center" title="Cerrar Sesión (Ctrl + Q)">
                <i class="bi bi-box-arrow-right text-xl"></i>
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    Q
                </span>
            </button>
        </form>

        <div class="w-px h-6 bg-white/20 mx-1"></div>

        {{-- Nav Links --}}
        @php
            $links = [
                ['route' => 'dashboard', 'icon' => 'bi-grid-1x2', 'active_icon' => 'bi-grid-1x2-fill', 'label' => 'Inicio', 'key' => '1'],
                ['route' => 'products.index', 'icon' => 'bi-box-seam', 'active_icon' => 'bi-box-seam-fill', 'label' => 'Productos', 'key' => '2'],
                ['route' => 'suppliers.index', 'icon' => 'bi-truck', 'active_icon' => 'bi-truck', 'label' => 'Proveedores', 'key' => '3'],
                ['route' => 'purchases.index', 'icon' => 'bi-cart-check', 'active_icon' => 'bi-cart-check-fill', 'label' => 'Compras', 'key' => '4'],
                ['route' => 'sales.index', 'icon' => 'bi-cash-stack', 'active_icon' => 'bi-cash-stack', 'label' => 'Ventas', 'key' => '5'],
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
               class="p-3 rounded-xl transition-all group relative flex items-center justify-center {{ $isActive ? 'bg-white/20 text-white shadow-inner' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
               title="{{ $link['label'] }}">
                <i class="bi {{ $currentIcon }} text-xl"></i>
                
                {{-- Shortcut Pill --}}
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    {{ $link['key'] }}
                </span>
            </a>
        @endforeach
    </div>
    
    <div class="text-[10px] text-gray-500 font-medium tracking-wide">
        Atajos: <span class="text-gray-400">Ctrl + 1..6</span> para navegar | <span class="text-gray-400">Ctrl + Q</span> para salir
    </div>
</div>
