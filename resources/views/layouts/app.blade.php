<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $navStyle = session('nav_style', config('ui.nav_style', 'dock'));
        $hideNav = request()->routeIs('*.create') || request()->routeIs('*.edit');
    @endphp
    <body class="bg-[#0f1117] text-[#f5f6fa] antialiased min-h-screen font-['Inter'] {{ $navStyle === 'dock' && !$hideNav ? 'pb-24' : '' }} {{ $hideNav ? 'overflow-hidden' : 'overflow-x-hidden' }}">
        <div class="flex min-h-screen">
            @if($navStyle === 'sidebar' && !$hideNav)
                <!-- Sidebar -->
                <x-sidebar />
            @endif

            <!-- Main Layout -->
            <div id="main-layout" class="flex-1 flex flex-col transition-all duration-300 ease-in-out {{ $navStyle === 'sidebar' && !$hideNav ? 'ml-[220px]' : '' }}">
                <!-- Topbar -->
                <x-topbar />

                <!-- Main Content -->
                <main class="flex-1 p-8">
                    <!-- Toast Container -->
                    <x-toast-container />
                    <x-flash-messages />

                    @yield('content')
                </main>
            </div>
        </div>
        
        @if($navStyle === 'dock' && !$hideNav)
            <x-navigation-dock />
        @endif

        @stack('scripts')
    </body>
</html>
