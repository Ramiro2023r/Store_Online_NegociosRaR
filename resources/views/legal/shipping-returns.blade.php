@extends('layouts.app')
@section('title', 'Envío y Devoluciones - Negocios RaR')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-2">Envío y Devoluciones</h1>
    <p class="text-gray-500 mb-8">Toda la información sobre tiempos de entrega, costos y política de devoluciones.</p>

    {{-- Información de envío --}}
    <div class="bg-white border rounded-xl p-6 mb-6">
        <h2 class="text-xl font-bold mb-3 text-rar-700">🚚 Información de envío</h2>
        <div class="prose prose-sm max-w-none text-gray-600">
            {{ nl2br(e(App\Models\Setting::getValue('shipping_info'))) }}
        </div>
    </div>

    {{-- Política de devoluciones --}}
    <div class="bg-white border rounded-xl p-6 mb-6">
        <h2 class="text-xl font-bold mb-3 text-rar-700">↩️ Política de devoluciones</h2>
        <div class="prose prose-sm max-w-none text-gray-600">
            {{ nl2br(e(App\Models\Setting::getValue('returns_policy'))) }}
        </div>
    </div>

    {{-- FAQ --}}
    <div class="bg-white border rounded-xl p-6">
        <h2 class="text-xl font-bold mb-3 text-rar-700">❓ Preguntas frecuentes</h2>
        @php
            $faqCategories = [
                'envio' => '🚚 Envío',
                'pago' => '💳 Pago',
                'devolucion' => '↩️ Devoluciones',
                'general' => '📋 General',
            ];
        @endphp

        <div x-data="{ open: null }" class="space-y-2">
            @foreach($faqs as $faq)
                <div class="border rounded-lg overflow-hidden">
                    <button @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-left hover:bg-gray-50">
                        <span>
                            <span class="text-xs text-gray-400 mr-2">{{ $faqCategories[$faq->category] ?? $faq->category }}</span>
                            {{ $faq->question }}
                        </span>
                        <svg class="h-4 w-4 shrink-0 transition-transform" :class="open === {{ $faq->id }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open === {{ $faq->id }}" x-collapse class="px-4 pb-3 text-sm text-gray-500">
                        {{ $faq->answer }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Contacto --}}
    <div class="bg-rar-50 rounded-xl p-6 mt-6 text-center">
        <p class="text-sm text-gray-600">¿No encuentras lo que buscas?</p>
        <a href="{{ route('contact.index') }}" class="inline-block mt-2 bg-rar-600 text-white font-semibold px-5 py-2 rounded-lg text-sm hover:bg-rar-700">Contáctanos</a>
    </div>
</div>
@endsection
