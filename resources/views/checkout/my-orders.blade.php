@extends('layouts.app')
@section('title', 'Mis pedidos - Negocios RaR')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">📦 Mis pedidos</h1>

    @if($orders->isEmpty())
        <p class="text-gray-500">Aún no tienes pedidos.</p>
    @else
        <div class="space-y-3">
            @foreach($orders as $order)
                <div class="bg-white border rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <div class="font-semibold">#{{ $order->order_number }}</div>
                        <div class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="text-sm px-3 py-1 rounded-full font-medium
                        {{ $order->status == 'entregado' ? 'bg-green-100 text-green-700' : ($order->status == 'cancelado' ? 'bg-red-100 text-red-700' : 'bg-cobre-100 text-cobre-700') }}">
                        {{ $order->statusLabel() }}
                    </div>
                    <div class="font-bold text-rar-600">S/ {{ number_format($order->total,2) }}</div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
