<header class="sticky top-0 z-30 flex min-h-16 items-center justify-between gap-4 border-b border-line bg-canvas px-[var(--space-page)]">
    <!-- Left: Page Indicator -->
    <div class="flex min-w-0 flex-1 items-center gap-3">
        @if(session('nav_style', config('ui.nav_style', 'dock')) === 'sidebar')
            <button type="button" class="icon-button md:hidden" aria-label="Abrir navegación" onclick="window.openSidebar?.()"><i class="bi bi-list text-xl"></i></button>
        @endif
        <!-- Page Indicator -->
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center border border-line bg-surface-raised text-accent">
                @php
                    $icon = 'bi-grid-1x2';
                    $title = 'Dashboard';
                    
                    if(request()->routeIs('products.*')) { $icon = 'bi-box-seam'; $title = 'Productos'; }
                    elseif(request()->routeIs('suppliers.*')) { $icon = 'bi-truck'; $title = 'Proveedores'; }
                    elseif(request()->routeIs('clients.*')) { $icon = 'bi-people'; $title = 'Clientes'; }
                    elseif(request()->routeIs('purchases.*')) { $icon = 'bi-cart-check'; $title = 'Compras'; }
                    elseif(request()->routeIs('pos')) { $icon = 'bi-shop'; $title = 'Punto de Venta'; }
                    elseif(request()->routeIs('sales.*')) { $icon = 'bi-cash-stack'; $title = 'Ventas'; }
                    elseif(request()->routeIs('profile.*')) { $icon = 'bi-person-badge'; $title = 'Perfil'; }
                @endphp
                <i class="bi {{ $icon }} text-accent text-lg"></i>
            </div>
            <div class="hidden sm:block">
                <span class="block text-[10px] font-semibold uppercase tracking-wider text-neutral-400">Ubicación</span>
                <span class="block text-sm font-semibold text-white">{{ $title }}</span>
            </div>
        </div>
    </div>

    <!-- Right: Icons & Profile -->
    <div class="flex items-center gap-1 sm:gap-2">
        <form action="{{ route('ui.toggle-nav') }}" method="POST">
            @csrf
            <button type="submit" class="icon-button" title="Cambiar estilo de navegación" aria-label="Cambiar estilo de navegación">
                <i class="bi bi-layout-sidebar-inset text-xl"></i>
            </button>
        </form>
        
        <button class="icon-button hidden sm:inline-flex" aria-label="Ayuda">
            <i class="bi bi-question-circle text-xl"></i>
        </button>
        <a href="{{ route('settings.show') }}" class="icon-button hidden sm:inline-flex" aria-label="Configuración">
            <i class="bi bi-gear text-xl"></i>
        </a>
        <button class="icon-button relative" aria-label="Notificaciones">
            <i class="bi bi-bell text-xl"></i>
            <span class="absolute right-2 top-2 h-2 w-2 bg-danger" title="Notificaciones sin leer" aria-hidden="true"></span>
        </button>

        <div class="mx-1 hidden h-8 w-px bg-line sm:block"></div>

        <a href="{{ route('profile.edit') }}" class="group flex items-center gap-2 pl-1" aria-label="Abrir perfil de {{ Auth::user()->name ?? 'Usuario' }}">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-semibold leading-tight text-white">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-400">Administrador</p>
            </div>

            <div class="relative">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=161616&color=ffffff&bold=true"
                     class="h-10 w-10 object-cover">
                <div class="absolute -bottom-0.5 -right-0.5 h-2 w-2 bg-success" title="En línea" aria-label="En línea" role="status"></div>
            </div>
        </a>
    </div>
</header>
