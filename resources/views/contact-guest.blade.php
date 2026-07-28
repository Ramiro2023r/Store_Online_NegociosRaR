@extends('layouts.app')
@section('title', 'Contáctanos - Negocios RaR')

@section('content')
<div class="max-w-md mx-auto px-4 py-20 text-center">
    <div class="text-6xl mb-4">🔒</div>
    <h1 class="text-xl font-bold mb-2">Inicia sesión para chatear con nosotros</h1>
    <p class="text-gray-500 text-sm mb-6">El chat de soporte está disponible únicamente para usuarios registrados. Inicia sesión o crea una cuenta gratis para contactarnos.</p>
    <div class="flex gap-3 justify-center">
        <a href="{{ route('login') }}" class="bg-rar-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-rar-700">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="border border-rar-600 text-rar-600 font-semibold px-6 py-2.5 rounded-lg hover:bg-rar-50">Registrarme</a>
    </div>
</div>
@endsection
