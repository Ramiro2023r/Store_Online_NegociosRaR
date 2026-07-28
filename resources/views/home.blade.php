@extends('layouts.app')
@section('title', 'Negocios RaR - Inicio')

@section('content')

{{-- Carrusel --}}
@php $slidesCount = $banners->count(); @endphp
<section class="relative bg-gray-900 overflow-hidden" x-data="{ slide: 0, slides: {{ $slidesCount ?: 1 }} }" x-init="setInterval(() => slide = (slide + 1) % slides, 4000)">
    <div class="max-w-7xl mx-auto">
        <div class="relative h-64 md:h-96">
            @forelse($banners as $i => $banner)
                <div class="absolute inset-0 flex items-center justify-between px-8 md:px-16 bg-gradient-to-r transition-opacity duration-700 {{ $banner->gradient_from }} {{ $banner->gradient_to }} {{ $banner->text_color }}"
                     :class="slide === {{ $i }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                    <div class="max-w-lg">
                        @if($banner->button_text || $banner->subtitle)
                            <span class="bg-white/20 text-xs font-semibold px-3 py-1 rounded-full">{{ $banner->subtitle ?? 'NUEVO' }}</span>
                        @endif
                        <h2 class="text-2xl md:text-4xl font-bold mt-3">{{ $banner->title }}</h2>
                        <a href="{{ $banner->button_url ?: '#' }}" class="inline-block mt-4 bg-white font-semibold px-5 py-2 rounded-full text-sm hover:bg-gray-100 {{ $banner->text_color === 'text-white' ? 'text-rar-700' : 'text-rar-600' }}">
                            {{ $banner->button_text ?: 'Ver más' }}
                        </a>
                    </div>
                    @if($banner->image)
                        <div class="hidden md:block w-72 h-72 bg-cover bg-center rounded-2xl" style="background-image: url('{{ asset('storage/'.$banner->image) }}')"></div>
                    @endif
                </div>
            @empty
                <div class="absolute inset-0 flex items-center justify-between px-8 md:px-16 bg-gradient-to-r from-rar-700 to-rar-500 text-white">
                    <div class="max-w-lg">
                        <h2 class="text-2xl md:text-4xl font-bold">Bienvenido a Negocios RaR</h2>
                        <p class="mt-2 text-white/90">Agrega banners desde el panel administrativo.</p>
                    </div>
                </div>
            @endforelse

            @if($slidesCount > 1)
                <button @click="slide = (slide + slides - 1) % slides" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 bg-white/30 hover:bg-white/50 rounded-full p-2">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button @click="slide = (slide + 1) % slides" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 bg-white/30 hover:bg-white/50 rounded-full p-2">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>

                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                    <template x-for="i in slides">
                        <button @click="slide = i - 1" class="h-2 rounded-full transition-all" :class="slide === i - 1 ? 'w-6 bg-white' : 'w-2 bg-white/50'"></button>
                    </template>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- Categorías --}}
<section class="max-w-7xl mx-auto px-4 py-10">
    <h2 class="text-xl font-bold mb-6">{{ App\Models\Setting::getValue('home_title_categories', 'Compra por categoría') }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="bg-white border rounded-xl p-4 text-center hover:shadow-md hover:border-rar-400 transition group">
                <div class="text-3xl mb-2">{{ $cat->icon }}</div>
                <div class="text-sm font-semibold group-hover:text-rar-600">{{ $cat->name }}</div>
                <div class="text-xs text-gray-400">{{ $cat->products_count }} productos</div>
            </a>
        @endforeach
    </div>
</section>

{{-- Destacados --}}
@if($featured->count())
<section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold">{{ App\Models\Setting::getValue('home_title_featured', '⭐ Productos destacados') }}</h2>
        <a href="{{ route('products.index') }}" class="text-rar-600 text-sm font-semibold hover:underline">Ver todos →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($featured as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- Nuevos --}}
@if($newest->count())
<section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold">{{ App\Models\Setting::getValue('home_title_newest', '🆕 Recién llegados') }}</h2>
        <a href="{{ route('products.index') }}" class="text-rar-600 text-sm font-semibold hover:underline">Ver todos →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($newest as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- Beneficios --}}
<section class="bg-white border-t mt-10">
    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        @forelse($benefits as $benefit)
            <div>
                <div class="text-3xl mb-2">{{ $benefit->icon }}</div>
                <div class="font-semibold text-sm">{{ $benefit->title }}</div>
            </div>
        @empty
            <div class="col-span-4 text-gray-400 text-sm">Agrega beneficios desde el panel administrativo.</div>
        @endforelse
    </div>
</section>

@endsection
