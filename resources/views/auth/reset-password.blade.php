@extends('layouts.app')
@section('title', 'Restablecer contraseña - Negocios RaR')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="text-center mb-8">
        <img src="{{ asset('images/Mejoradelogo.svg') }}" class="h-12 mx-auto mb-4">
        <h1 class="text-xl font-bold">Restablece tu contraseña</h1>
    </div>

    <form action="{{ route('password.update') }}" method="POST" class="bg-white border rounded-xl p-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="text-sm font-medium">Correo electrónico</label>
            <input type="email" name="email" value="{{ $email }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Nueva contraseña</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        @error('email')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
        <button class="w-full bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">Restablecer contraseña</button>
    </form>
</div>
@endsection
