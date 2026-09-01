<header class="h-[72px] bg-[#0f1117] text-[#f5f6fa] flex items-center justify-between px-8 border-b border-white/5 sticky top-0 z-40 backdrop-blur-md bg-opacity-80">
    <!-- Left: Page Indicator & Search Bar -->
    <div class="flex items-center gap-6 flex-1 max-w-2xl">
        <!-- Page Indicator -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600/10 rounded-xl flex items-center justify-center border border-blue-500/20">
                @php
                    $icon = 'bi-grid-1x2';
                    $title = 'Dashboard';
                    
                    if(request()->routeIs('products.*')) { $icon = 'bi-box-seam'; $title = 'Productos'; }
                    elseif(request()->routeIs('suppliers.*')) { $icon = 'bi-truck'; $title = 'Proveedores'; }
                    elseif(request()->routeIs('clients.*')) { $icon = 'bi-people'; $title = 'Clientes'; }
                    elseif(request()->routeIs('purchases.*')) { $icon = 'bi-cart-check'; $title = 'Compras'; }
                    elseif(request()->routeIs('sales.*')) { $icon = 'bi-cash-stack'; $title = 'Ventas'; }
                    elseif(request()->routeIs('profile.*')) { $icon = 'bi-person-badge'; $title = 'Perfil'; }
                @endphp
                <i class="bi {{ $icon }} text-blue-500 text-lg"></i>
            </div>
            <div class="hidden md:block">
                <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] leading-none block mb-0.5">Ubicación</span>
                <span class="text-sm font-bold text-white tracking-tight leading-none">{{ $title }}</span>
            </div>
        </div>

        <div class="h-8 w-px bg-white/5 mx-2"></div>

        <!-- Search Bar -->
        <div class="flex-1">
            @php
                $placeholder = 'Búsqueda global premium...';
                if(request()->routeIs('products.*')) $placeholder = 'Buscar en productos...';
                elseif(request()->routeIs('suppliers.*')) $placeholder = 'Buscar proveedores...';
                elseif(request()->routeIs('clients.*')) $placeholder = 'Buscar en clientes...';
                elseif(request()->routeIs('purchases.*')) $placeholder = 'Buscar historial de compras...';
                elseif(request()->routeIs('sales.*')) $placeholder = 'Buscar historial de ventas...';
            @endphp
            <div class="relative group">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-500 transition-colors"></i>
                <input type="text" placeholder="{{ $placeholder }}" 
                       class="w-full bg-white/5 border border-white/5 rounded-2xl pl-12 pr-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-gray-600">
            </div>
        </div>
    </div>

    <!-- Right: Icons & Profile -->
    <div class="flex items-center gap-2 lg:gap-4">
        <form action="{{ route('ui.toggle-nav') }}" method="POST">
            @csrf
            <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/5 text-gray-400 hover:text-white transition-all" title="Cambiar estilo de navegación">
                <i class="bi bi-layout-sidebar-inset text-xl"></i>
            </button>
        </form>
        
        <button class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/5 text-gray-400 hover:text-white transition-all">
            <i class="bi bi-question-circle text-xl"></i>
        </button>
        <button class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/5 text-gray-400 hover:text-white transition-all">
            <i class="bi bi-gear text-xl"></i>
        </button>
        <button class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/5 text-gray-400 hover:text-white transition-all relative">
            <i class="bi bi-bell text-xl"></i>
            <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-[#0f1117]"></span>
        </button>

        <div class="h-8 w-px bg-white/10 mx-2"></div>

        <div class="flex items-center gap-3 group cursor-pointer pl-2">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-200 leading-tight">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Administrador</p>
            </div>

            <div class="relative">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=2563eb&color=fff&bold=true" 
                     class="w-10 h-10 rounded-xl object-cover border border-white/10 shadow-lg group-hover:scale-105 transition-transform">
                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-[#0f1117]"></div>
            </div>
        </div>
    </div>
</header>
