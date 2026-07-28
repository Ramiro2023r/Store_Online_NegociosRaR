@extends('layouts.app')
@section('title', 'Recuperar contraseña - Negocios RaR')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="text-center mb-8">
        <img src="{{ asset('images/Mejoradelogo.svg') }}" class="h-12 mx-auto mb-4">
        <h1 class="text-xl font-bold">Recupera tu contraseña</h1>
        <p class="text-sm text-gray-500 mt-2">Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>
    </div>

    <form action="{{ route('password.email') }}" method="POST" class="bg-white border rounded-xl p-6 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Correo electrónico</label>
            <input type="email" name="email" required autofocus class="w-full border rounded-lg px-3 py-2 mt-1">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <button class="w-full bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">Enviar enlace de recuperación</button>
        <p class="text-center text-sm"><a href="{{ route('login') }}" class="text-rar-600 hover:underline">← Volver a iniciar sesión</a></p>
    </form>
</div>
@endsection
