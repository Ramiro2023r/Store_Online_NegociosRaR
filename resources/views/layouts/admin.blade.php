<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo - Negocios RaR')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $favicon = App\Models\Setting::getValue('store_logo_icon', 'images/Mejoradelogoiconoapp.svg'); @endphp
    <link rel="icon" type="image/svg+xml" href="{{ str_starts_with($favicon, 'http') ? $favicon : asset(str_starts_with($favicon, 'images/') ? $favicon : 'storage/'.$favicon) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { rar: { 50:'#EEF3FB',100:'#DCE7F6',400:'#5F8BCD',500:'#2F6FB0',600:'#1B4F91',700:'#0D3B6E',900:'#0F1F3D' }, cobre: { 50:'#F7F1EC',100:'#EFE3DA',200:'#E0CCBD',500:'#B0876C',600:'#9A6F56',700:'#7D5A45',800:'#604536' } } } } }
    </script>
    <script defer src="//unpkg.com/alpinejs"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col shrink-0">
        <div class="p-5 border-b border-gray-800">
            <img src="{{ str_starts_with($favicon, 'http') ? $favicon : asset(str_starts_with($favicon, 'images/') ? $favicon : 'storage/'.$favicon) }}" class="h-9">
            <p class="text-xs text-gray-500 mt-1">Panel Administrativo</p>
        </div>
        <nav class="flex-1 p-3 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-rar-600 text-white' : '' }}">📊 Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.products.*') ? 'bg-rar-600 text-white' : '' }}">📦 Productos</a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.categories.*') ? 'bg-rar-600 text-white' : '' }}">🗂️ Categorías</a>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.orders.*') ? 'bg-rar-600 text-white' : '' }}">🧾 Ventas / Pedidos</a>
            <a href="{{ route('admin.messages.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.messages.*') ? 'bg-rar-600 text-white' : '' }}">💬 Mensajes</a>
            <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.reviews.*') ? 'bg-rar-600 text-white' : '' }}">⭐ Reseñas</a>
            <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.inventory.*') ? 'bg-rar-600 text-white' : '' }}">📦 Inventario</a>
            <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.coupons.*') ? 'bg-rar-600 text-white' : '' }}">🏷️ Cupones</a>
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.reports.*') ? 'bg-rar-600 text-white' : '' }}">📊 Reportes</a>
            <a href="{{ route('admin.newsletters.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.newsletters.*') ? 'bg-rar-600 text-white' : '' }}">📧 Newsletter</a>
            <a href="{{ route('admin.settings.index') }}#loyalty" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.settings.*') ? 'bg-rar-600 text-white' : '' }}">⭐ Puntos fidelización</a>

            {{-- Configuración de tienda (dropdown) --}}
            @php $cfgOpen = request()->routeIs('admin.banners.*') || request()->routeIs('admin.benefits.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.settings.*'); @endphp
            <div class="border-t border-gray-800 my-2"></div>
            <div x-data="{ open: {{ $cfgOpen ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                    <span>📋 Configuración de tienda</span>
                    <svg class="h-3 w-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open" x-collapse class="space-y-1 mt-1">
                    <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.banners.*') ? 'bg-rar-600 text-white' : '' }}">🖼️ Banners / Carrusel</a>
                    <a href="{{ route('admin.benefits.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.benefits.*') ? 'bg-rar-600 text-white' : '' }}">✅ Beneficios</a>
                    <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.faqs.*') ? 'bg-rar-600 text-white' : '' }}">❓ FAQ / Ayuda</a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.settings.*') ? 'bg-rar-600 text-white' : '' }}">⚙️ Configuración general</a>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-rar-600 text-white' : '' }}">👥 Usuarios</a>
            @endif
            <div class="border-t border-gray-800 my-2"></div>
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800">🏪 Ver tienda</a>
        </nav>
        <div class="p-4 border-t border-gray-800">
            <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
            <div class="text-xs text-gray-500 capitalize mb-2">{{ auth()->user()->role }}</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-xs text-red-400 hover:text-red-300">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col">
        <header class="bg-white border-b px-6 py-4">
            <h1 class="text-lg font-bold">@yield('page-title', 'Panel Administrativo')</h1>
        </header>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@include('partials.assistant-chat')
</body>
</html>
