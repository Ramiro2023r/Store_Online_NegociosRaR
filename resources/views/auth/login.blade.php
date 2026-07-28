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

        <p class="text-center text-sm text-gray-500">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-rar-600 font-semibold hover:underline">Regístrate aquí</a></p>
    </form>

    <div class="mt-4 text-xs text-gray-400 text-center bg-gray-50 rounded-lg p-3">
        <strong>Cuentas demo:</strong><br>
        Admin: admin@negociosrar.com / admin123<br>
        Trabajador: trabajador@negociosrar.com / trabajador123<br>
        Cliente: cliente@negociosrar.com / cliente123
    </div>
</div>
@endsection
