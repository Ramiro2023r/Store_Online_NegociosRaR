@extends('layouts.app')
@section('title', 'Crear cuenta - Negocios RaR')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="text-center mb-8">
        <img src="{{ asset('images/Mejoradelogo.svg') }}" class="h-12 mx-auto mb-4">
        <h1 class="text-xl font-bold">Crea tu cuenta</h1>
    </div>

    <form action="{{ route('register') }}" method="POST" class="bg-white border rounded-xl p-6 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Nombre completo</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full border rounded-lg px-3 py-2 mt-1">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-medium">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-medium">Teléfono (opcional)</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Contraseña</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 mt-1">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-medium">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>

        <div>
            <label class="flex items-start gap-2 text-sm text-gray-600">
                <input type="checkbox" name="accept_terms" value="1" class="mt-1 shrink-0">
                <span>He leído y acepto la <a href="{{ route('privacy-policy') }}" target="_blank" class="text-rar-600 font-semibold hover:underline">Política de Privacidad</a> y los <a href="{{ route('terms-conditions') }}" target="_blank" class="text-rar-600 font-semibold hover:underline">Términos y Condiciones</a></span>
            </label>
            @error('accept_terms')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <button class="w-full bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">Crear cuenta</button>
        <p class="text-center text-sm text-gray-500">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-rar-600 font-semibold hover:underline">Inicia sesión</a></p>
    </form>
</div>
@endsection
