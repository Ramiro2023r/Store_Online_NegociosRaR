@extends('layouts.app')
@section('title', 'Iniciar sesión - Negocios RaR')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="text-center mb-8">
        <img src="{{ asset('images/Mejoradelogo.svg') }}" class="h-12 mx-auto mb-4">
        <h1 class="text-xl font-bold">Inicia sesión en tu cuenta</h1>
    </div>

    <form action="{{ route('login') }}" method="POST" class="bg-white border rounded-xl p-6 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Contraseña</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        @error('email')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="remember"> Recordarme</label>
            <a href="{{ route('password.request') }}" class="text-rar-600 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>

        <button class="w-full bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">Iniciar sesión</button>

        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t"></div></div>
            <div class="relative flex justify-center text-sm"><span class="bg-white px-3 text-gray-400">O continúa con</span></div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('socialite.redirect', 'google') }}" class="flex-1 flex items-center justify-center gap-2 border rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Google
            </a>
            <a href="{{ route('socialite.redirect', 'facebook') }}" class="flex-1 flex items-center justify-center gap-2 border rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition text-[#1877F2]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>
        </div>
        <p class="text-center text-sm text-gray-500 mt-4">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-rar-600 font-semibold hover:underline">Regístrate aquí</a></p>
    </form>

    @error('social')<p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>@enderror

    <div class="mt-4 text-xs text-gray-400 text-center bg-gray-50 rounded-lg p-3">
        <strong>Cuentas demo:</strong><br>
        Admin: admin@negociosrar.com / admin123<br>
        Trabajador: trabajador@negociosrar.com / trabajador123<br>
        Cliente: cliente@negociosrar.com / cliente123
    </div>
</div>
@endsection
