<div id="sidebarBackdrop" class="fixed inset-0 z-40 hidden bg-black/60 md:hidden" aria-hidden="true"></div>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col overflow-x-hidden border-r border-line bg-surface text-white transition-all duration-200 md:translate-x-0">
    <div class="flex h-16 items-center justify-between px-4">
        <a href="{{ route('dashboard') }}" class="sidebar-brand text-lg font-bold tracking-tight">PetShop <span class="text-primary">Pro</span></a>
        <button id="sidebarToggle" type="button" class="icon-button" aria-label="Contraer navegación" aria-expanded="true">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>
    <nav class="flex flex-1 flex-col gap-5 px-3" aria-label="Navegación principal">
        <div class="space-y-1">
            <p class="sidebar-section px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-neutral-400">Inicio</p>
            <a href="{{ route('dashboard') }}" title="Inicio" class="sidebar-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <i class="bi bi-grid-1x2 min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Inicio</span>
            </a>
        </div>

        <div class="space-y-1">
            <p class="sidebar-section px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-neutral-400">Operaciones</p>
            <a href="{{ route('pos') }}" title="Punto de Venta" class="sidebar-link {{ request()->routeIs('pos') ? 'is-active' : '' }}">
                <i class="bi bi-shop min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Punto de Venta</span>
            </a>
            <a href="{{ route('sales.index') }}" title="Ventas" class="sidebar-link {{ request()->routeIs('sales.*') ? 'is-active' : '' }}">
                <i class="bi bi-cash-stack min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Ventas</span>
            </a>
            <a href="{{ route('purchases.index') }}" title="Compras" class="sidebar-link {{ request()->routeIs('purchases.*') ? 'is-active' : '' }}">
                <i class="bi bi-cart-check min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Compras</span>
            </a>
        </div>

        <div class="space-y-1">
            <p class="sidebar-section px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-neutral-400">Catálogo</p>
            <a href="{{ route('products.index') }}" title="Productos" class="sidebar-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}">
                <i class="bi bi-box-seam min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Productos</span>
            </a>
            <a href="{{ route('suppliers.index') }}" title="Proveedores" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'is-active' : '' }}">
                <i class="bi bi-truck min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Proveedores</span>
            </a>
        </div>

        <div class="space-y-1">
            <p class="sidebar-section px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-neutral-400">Clientes</p>
            <a href="{{ route('clients.index') }}" title="Clientes" class="sidebar-link {{ request()->routeIs('clients.*') ? 'is-active' : '' }}">
                <i class="bi bi-people min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Clientes</span>
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-auto pb-4">
            @csrf
            <button type="submit" title="Cerrar sesión" class="sidebar-link w-full text-left hover:bg-danger/10 hover:text-white">
                <i class="bi bi-box-arrow-right min-w-6 text-center text-lg" aria-hidden="true"></i><span class="sidebar-text">Cerrar sesión</span>
            </button>
        </form>
    </nav>
</aside>

<style>
    .sidebar-link { display: flex; min-height: 2.75rem; align-items: center; gap: .75rem; border-radius: 0; padding: .65rem .75rem; color: #a3a3a3; font-size: .875rem; font-weight: 600; transition: background-color .2s, color .2s; }
    .sidebar-link:hover { background: #222222; color: #ffffff; }
    .sidebar-link.is-active { background: #222222; color: #ffffff; }
    .sidebar-link.is-active i { color: #8d8d8d; }
    @media (min-width: 768px) {
        #sidebar.is-collapsed { width: 4.5rem; }
        #sidebar.is-collapsed .sidebar-text, #sidebar.is-collapsed .sidebar-brand, #sidebar.is-collapsed .sidebar-section { display: none; }
        #sidebar.is-collapsed .sidebar-link { justify-content: center; padding-inline: .75rem; }
        #main-layout.is-collapsed { margin-left: 4.5rem; }
        #sidebar.is-collapsed .sidebar-link { position: relative; }
        #sidebar.is-collapsed .sidebar-link:hover::after { content: attr(title); position: absolute; left: 4.25rem; z-index: 60; white-space: nowrap; border-radius: 0; background: #161616; border: 1px solid #262626; padding: .5rem .65rem; color: #ffffff; font-size: .75rem; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const layout = document.getElementById('main-layout');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (!sidebar || !toggle || !layout) return;

        const setMobile = (open) => {
            sidebar.classList.toggle('-translate-x-full', !open);
            backdrop?.classList.toggle('hidden', !open);
        };

        if (sessionStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('is-collapsed');
            layout.classList.add('is-collapsed');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                setMobile(false);
                return;
            }
            const collapsed = sidebar.classList.toggle('is-collapsed');
            layout.classList.toggle('is-collapsed', collapsed);
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('aria-label', collapsed ? 'Expandir navegación' : 'Contraer navegación');
            sessionStorage.setItem('sidebarCollapsed', String(collapsed));
        });
        backdrop?.addEventListener('click', () => setMobile(false));
        window.addEventListener('resize', () => { if (window.innerWidth >= 768) setMobile(true); });
        window.openSidebar = () => setMobile(true);
    });
</script>
