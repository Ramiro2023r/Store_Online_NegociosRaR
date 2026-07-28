@extends('layouts.app')
@section('title', 'Acerca de nosotros - Negocios RaR')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <div class="text-center mb-12">
        @php $mainLogo = App\Models\Setting::getValue('store_logo', 'images/Mejoradelogo.svg'); @endphp
        <img src="{{ str_starts_with($mainLogo, 'http') ? $mainLogo : asset(str_starts_with($mainLogo, 'images/') ? $mainLogo : 'storage/'.$mainLogo) }}" class="h-14 mx-auto mb-4">
        <h1 class="text-3xl font-bold">Acerca de Negocios RaR</h1>
        <p class="text-gray-500 mt-2 max-w-2xl mx-auto">{{ App\Models\Setting::getValue('about_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white border rounded-xl p-6 text-center">
            <div class="text-3xl mb-2">🎯</div>
            <h3 class="font-bold mb-2">Nuestra misión</h3>
            <p class="text-sm text-gray-500">{{ App\Models\Setting::getValue('about_mission', 'Facilitar el acceso a productos de calidad para todos los peruanos, con precios justos y un servicio excepcional.') }}</p>
        </div>
        <div class="bg-white border rounded-xl p-6 text-center">
            <div class="text-3xl mb-2">🚀</div>
            <h3 class="font-bold mb-2">Nuestra visión</h3>
            <p class="text-sm text-gray-500">{{ App\Models\Setting::getValue('about_vision', 'Ser la tienda online líder en Perú, reconocida por la confianza de nuestros clientes y la calidad de nuestros productos.') }}</p>
        </div>
        <div class="bg-white border rounded-xl p-6 text-center">
            <div class="text-3xl mb-2">💛</div>
            <h3 class="font-bold mb-2">Nuestros valores</h3>
            <p class="text-sm text-gray-500">{{ App\Models\Setting::getValue('about_values', 'Honestidad, transparencia, calidad y compromiso con la satisfacción total de nuestros clientes.') }}</p>
        </div>
    </div>

    <div class="bg-rar-50 rounded-2xl p-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div>
            <div class="text-3xl font-bold text-rar-600">{{ App\Models\Setting::getValue('about_clients_count', '+15,000') }}</div>
            <div class="text-sm text-gray-500">Clientes felices</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-rar-600">{{ App\Models\Setting::getValue('about_products_count', '+500') }}</div>
            <div class="text-sm text-gray-500">Productos disponibles</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-rar-600">{{ App\Models\Setting::getValue('about_regions_count', '24') }}</div>
            <div class="text-sm text-gray-500">Regiones con envío</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-rar-600">{{ App\Models\Setting::getValue('about_rating', '4.8★') }}</div>
            <div class="text-sm text-gray-500">Calificación promedio</div>
        </div>
    </div>
</div>
@endsection
