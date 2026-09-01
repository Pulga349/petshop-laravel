<aside id="sidebar" class="fixed top-0 left-0 z-50 h-screen transition-all duration-300 ease-in-out bg-[#242526] text-[#f5f6fa] w-[220px] flex flex-col items-center pt-4 shadow-lg overflow-x-hidden border-r border-gray-800">
    <button id="sidebarToggle" class="w-full px-5 mb-4 text-left outline-none cursor-pointer">
        <i class="bi bi-list text-2xl"></i>
    </button>
    
    <nav class="flex flex-col w-full px-2 gap-1.5">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-sm font-bold transition-all rounded-xl {{ !request()->routeIs('dashboard') ? 'hover:bg-white/5' : '' }} group {{ request()->routeIs('dashboard') ? 'text-white bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-gray-400' }}">
            <i class="bi bi-grid-1x2 mr-4 min-w-[22px] text-center text-lg"></i>
            <span class="sidebar-text whitespace-nowrap">Inicio</span>
        </a>
        
        <a href="{{ route('products.index') }}" class="flex items-center p-3 text-sm font-bold transition-all rounded-xl {{ !request()->routeIs('products.*') ? 'hover:bg-white/5' : '' }} group {{ request()->routeIs('products.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-gray-400' }}">
            <i class="bi bi-cart mr-4 min-w-[22px] text-center text-lg"></i>
            <span class="sidebar-text whitespace-nowrap">Productos</span>
        </a>

        <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-sm font-bold transition-all rounded-xl {{ !request()->routeIs('suppliers.*') ? 'hover:bg-white/5' : '' }} group {{ request()->routeIs('suppliers.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-gray-400' }}">
            <i class="bi bi-truck mr-4 min-w-[22px] text-center text-lg"></i>
            <span class="sidebar-text whitespace-nowrap">Proveedores</span>
        </a>

        <a href="{{ route('purchases.index') }}" class="flex items-center p-3 text-sm font-bold transition-all rounded-xl {{ !request()->routeIs('purchases.*') ? 'hover:bg-white/5' : '' }} group {{ request()->routeIs('purchases.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-gray-400' }}">
            <i class="bi bi-bag-check mr-4 min-w-[22px] text-center text-lg"></i>
            <span class="sidebar-text whitespace-nowrap">Compras</span>
        </a>

        <a href="{{ route('sales.index') }}" class="flex items-center p-3 text-sm font-bold transition-all rounded-xl {{ !request()->routeIs('sales.*') ? 'hover:bg-white/5' : '' }} group {{ request()->routeIs('sales.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-gray-400' }}">
            <i class="bi bi-cash-stack mr-4 min-w-[22px] text-center text-lg"></i>
            <span class="sidebar-text whitespace-nowrap">Ventas</span>
        </a>

        <a href="{{ route('clients.index') }}" class="flex items-center p-3 text-sm font-bold transition-all rounded-xl {{ !request()->routeIs('clients.*') ? 'hover:bg-white/5' : '' }} group {{ request()->routeIs('clients.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-600/20' : 'text-gray-400' }}">
            <i class="bi bi-people mr-4 min-w-[22px] text-center text-lg"></i>
            <span class="sidebar-text whitespace-nowrap">Clientes</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-auto pb-4">
            @csrf
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" 
               class="flex items-center p-3 text-sm font-bold text-gray-500 transition-all rounded-xl hover:bg-red-500/10 hover:text-red-500 group">
                <i class="bi bi-box-arrow-right mr-4 min-w-[22px] text-center text-lg"></i>
                <span class="sidebar-text whitespace-nowrap">Cerrar Sesión</span>
            </a>
        </form>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const layout = document.getElementById('main-layout');
        
        if (!sidebar || !toggle || !layout) return;
        
        // Restaurar estado colapsado desde sessionStorage
        if (sessionStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.remove('w-[220px]');
            sidebar.classList.add('w-[64px]');
            document.querySelectorAll('.sidebar-text').forEach(t => t.classList.add('opacity-0'));
            layout.classList.remove('ml-[220px]');
            layout.classList.add('ml-[64px]');
        }
        
        toggle.addEventListener('click', function() {
            const wasCollapsed = sidebar.classList.contains('w-[64px]');
            
            sidebar.classList.toggle('w-[220px]');
            sidebar.classList.toggle('w-[64px]');
            
            // Ocultar/mostrar textos al colapsar
            document.querySelectorAll('.sidebar-text').forEach(t => t.classList.toggle('opacity-0'));
            
            // Ajustar margen del layout
            layout.classList.toggle('ml-[220px]');
            layout.classList.toggle('ml-[64px]');
            
            // Persistir estado para que sobreviva navegación
            sessionStorage.setItem('sidebarCollapsed', wasCollapsed ? 'false' : 'true');
        });
    });
</script>
