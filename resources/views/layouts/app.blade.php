<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Negocios RaR - Tu tienda online de confianza')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/Mejoradelogoiconoapp.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        rar: { 50:'#EEF3FB',100:'#DCE7F6',400:'#5F8BCD',500:'#2F6FB0',600:'#1B4F91',700:'#0D3B6E',900:'#0F1F3D' },
                        cobre: { 50:'#F7F1EC',100:'#EFE3DA',200:'#E0CCBD',500:'#B0876C',600:'#9A6F56',700:'#7D5A45',800:'#604536' }
                    }
                }
            }
        }
    </script>
    <style>[x-cloak]{display:none!important}</style>
    <script defer src="//unpkg.com/alpinejs"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- Barra superior --}}
    <div class="bg-rar-900 text-white text-xs py-1.5 px-4 text-center hidden sm:block">
        📦 Envío gratis en compras mayores a S/ 200 &nbsp;|&nbsp; 🎉 Bienvenido a Negocios RaR
    </div>

    {{-- Navbar --}}
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/Mejoradelogo.svg') }}" alt="Negocios RaR" class="h-10">
                </a>

                <nav class="hidden md:flex items-center gap-6 font-medium text-sm">
                    <a href="{{ route('home') }}" class="hover:text-rar-600 {{ request()->routeIs('home') ? 'text-rar-600' : '' }}">Inicio</a>
                    <a href="{{ route('products.index') }}" class="hover:text-rar-600 {{ request()->routeIs('products.*') ? 'text-rar-600' : '' }}">Productos</a>
                    <a href="{{ route('about') }}" class="hover:text-rar-600 {{ request()->routeIs('about') ? 'text-rar-600' : '' }}">Acerca de</a>
                    <a href="{{ auth()->check() ? route('contact.index') : route('contact.guest') }}" class="hover:text-rar-600 {{ request()->routeIs('contact.*') ? 'text-rar-600' : '' }}">Contáctanos</a>
                </nav>

                <div class="flex items-center gap-4">
                    <form action="{{ route('products.index') }}" method="GET" class="hidden lg:block">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar productos..."
                            class="border rounded-full px-4 py-1.5 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-rar-500">
                    </form>

                    @auth
                        <a href="{{ route('wishlist.index') }}" class="relative">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                            </svg>
                            @if($wishlistCount > 0)
                                <span class="absolute -top-2 -right-2 bg-cobre-500 text-white text-xs font-bold min-w-[20px] h-5 flex items-center justify-center rounded-full px-1 leading-none">{{ min($wishlistCount, 99) }}{{ $wishlistCount > 99 ? '+' : '' }}</span>
                            @endif
                        </a>
                    @endauth

                    <a href="{{ route('cart.index') }}" class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.836l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.94-4.706 2.436-7.183.078-.393-.203-.75-.594-.75H5.106M7.5 14.25L5.106 5.25M7.5 14.25L5.25 21m14.25-1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM12.75 21a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-rar-600 text-white text-xs font-bold min-w-[20px] h-5 flex items-center justify-center rounded-full px-1 leading-none">{{ min($cartCount, 99) }}{{ $cartCount > 99 ? '+' : '' }}</span>
                        @endif
                    </a>

                    @auth
                        <div class="relative group">
                            <button class="flex items-center gap-1 text-sm font-medium">
                                <span class="hidden sm:inline">{{ Str::limit(auth()->user()->name, 15) }}</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 hidden group-hover:block">
                                @if(auth()->user()->isStaff())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">🛠️ Panel Administrativo</a>
                                    <div class="border-t my-1"></div>
                                @endif
                                <a href="{{ route('checkout.my-orders') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">📦 Mis pedidos</a>
                                <a href="{{ route('contact.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">💬 Chat de soporte</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600">Cerrar sesión</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold px-4 py-2 rounded-full bg-rar-600 text-white hover:bg-rar-700">
                            Iniciar sesión
                        </a>
                    @endauth

                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            </div>

            <div id="mobile-menu" class="hidden md:hidden pb-4 space-y-2 text-sm font-medium">
                <a href="{{ route('home') }}" class="block py-1">Inicio</a>
                <a href="{{ route('products.index') }}" class="block py-1">Productos</a>
                <a href="{{ route('about') }}" class="block py-1">Acerca de</a>
                <a href="{{ auth()->check() ? route('contact.index') : route('contact.guest') }}" class="block py-1">Contáctanos</a>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full" class="fixed top-4 right-4 z-50 max-w-sm w-full">
            <div class="bg-rar-600 text-white text-sm rounded-xl shadow-lg px-5 py-4 flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="shrink-0 text-white/70 hover:text-white">&times;</button>
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <img src="{{ asset('images/Mejoradelogoiconoapp.svg') }}" alt="Negocios RaR" class="h-10 mb-3">
                <p class="text-sm text-gray-400">Tu tienda online de confianza. Todo lo que necesitas, en un solo lugar.</p>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Tienda</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('products.index') }}" class="hover:text-white">Todos los productos</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white">Acerca de nosotros</a></li>
                    <li><a href="{{ auth()->check() ? route('contact.index') : route('contact.guest') }}" class="hover:text-white">Contáctanos</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Mi cuenta</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    @guest
                        <li><a href="{{ route('login') }}" class="hover:text-white">Iniciar sesión</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white">Crear cuenta</a></li>
                    @else
                        <li><a href="{{ route('checkout.my-orders') }}" class="hover:text-white">Mis pedidos</a></li>
                    @endguest
                    <li><a href="{{ route('cart.index') }}" class="hover:text-white">Carrito</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Contacto</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>📍 Lima, Perú</li>
                    <li>📞 (01) 555-0100</li>
                    <li>✉️ ventas@negociosrar.com</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 py-4 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} Negocios RaR. Todos los derechos reservados.
            &nbsp;|&nbsp; <a href="{{ route('privacy-policy') }}" class="hover:text-white">Política de Privacidad</a>
            &nbsp;|&nbsp; <a href="{{ route('terms-conditions') }}" class="hover:text-white">Términos y Condiciones</a>
        </div>
    </footer>
</body>
</html>
