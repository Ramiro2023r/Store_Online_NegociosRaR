@extends('layouts.app')
@section('title', 'Pedido confirmado - Negocios RaR')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">
    @if($order->payment_status === 'pagado')
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-2xl font-bold mb-2">¡Pago exitoso!</h1>
        <p class="text-gray-500 mb-2">Tu pedido <strong>#{{ $order->order_number }}</strong> fue registrado y pagado correctamente.</p>
        <p class="text-xs text-gray-400 mb-6">Transacción: {{ $order->culqi_charge_id }}</p>
    @else
        <div class="text-6xl mb-4">🎉</div>
        <h1 class="text-2xl font-bold mb-2">¡Gracias por tu compra!</h1>
        <p class="text-gray-500 mb-6">Tu pedido <strong>#{{ $order->order_number }}</strong> fue registrado correctamente. Te contactaremos para coordinar el pago y la entrega.</p>
    @endif
    <div class="bg-white border rounded-xl p-6 text-left mb-6">
        <div class="flex justify-between mb-2"><span class="text-gray-500">Total</span><span class="font-bold">S/ {{ number_format($order->total,2) }}</span></div>
        <div class="flex justify-between mb-2">
            <span class="text-gray-500">Estado</span>
            <span class="font-medium {{ $order->payment_status === 'pagado' ? 'text-green-600' : 'text-cobre-600' }}">{{ $order->statusLabel() }}</span>
        </div>
        <div class="flex justify-between"><span class="text-gray-500">Envío a</span><span class="font-medium">{{ $order->shipping_address }}</span></div>
    </div>
    <a href="{{ route('checkout.my-orders') }}" class="bg-rar-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-rar-700">Ver mis pedidos</a>
</div>
@endsection
