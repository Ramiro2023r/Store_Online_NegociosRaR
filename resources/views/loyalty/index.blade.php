@extends('layouts.app')
@section('title', 'Mis puntos - Negocios RaR')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">⭐ Mis puntos de fidelización</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-rar-600 text-white rounded-xl p-6 text-center">
            <div class="text-4xl font-bold">{{ number_format($user->loyalty_points) }}</div>
            <div class="text-sm opacity-80">Puntos disponibles</div>
        </div>
        <div class="bg-cobre-500 text-white rounded-xl p-6 text-center">
            <div class="text-4xl font-bold">{{ number_format($user->lifetime_points) }}</div>
            <div class="text-sm opacity-80">Puntos ganados (total)</div>
        </div>
        <div class="bg-white border rounded-xl p-6 text-center">
            <div class="text-4xl font-bold text-rar-600">S/ {{ number_format($user->loyalty_points * $redeemRate, 2) }}</div>
            <div class="text-sm text-gray-500">Valor de tus puntos</div>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6 mb-8">
        <h3 class="font-semibold mb-2">¿Cómo funciona?</h3>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
            <li>Ganas <strong>{{ $rate }} punto(s)</strong> por cada S/ 1 gastado en tus compras.</li>
            <li>Cada <strong>{{ $minPoints }} puntos</strong> equivalen a <strong>S/ {{ number_format($minPoints * $redeemRate, 2) }}</strong> de descuento.</li>
            <li>Puedes canjear tus puntos al momento de pagar en el checkout.</li>
            <li>Los puntos se acreditan automáticamente cuando tu pedido es marcado como <strong>Entregado</strong>.</li>
        </ul>
    </div>

    <div class="bg-white border rounded-xl">
        <div class="p-4 border-b font-bold">Historial de transacciones</div>
        @forelse($user->loyaltyTransactions as $tx)
            <div class="flex items-center justify-between px-4 py-3 border-b last:border-0 text-sm">
                <div>
                    <div class="font-medium {{ $tx->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $tx->points > 0 ? '+' . number_format($tx->points) : number_format($tx->points) }} puntos
                    </div>
                    <div class="text-xs text-gray-400">{{ $tx->description ?? $tx->type }}</div>
                </div>
                <div class="text-xs text-gray-400">{{ $tx->created_at->format('d/m/Y') }}</div>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400">Aún no tienes transacciones. ¡Haz tu primera compra para ganar puntos!</div>
        @endforelse
    </div>
</div>
@endsection
