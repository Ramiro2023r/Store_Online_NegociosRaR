@extends('layouts.app')
@section('title', 'Pedido #' . $order->order_number . ' - Negocios RaR')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('checkout.my-orders') }}" class="text-sm text-rar-600 hover:underline">&larr; Mis pedidos</a>
    <h1 class="text-2xl font-bold mt-2 mb-6">📦 Pedido #{{ $order->order_number }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border rounded-xl p-6">
                <h3 class="font-bold mb-4">Estado del pedido</h3>
                @include('partials.order-timeline')
            </div>

            <div class="bg-white border rounded-xl p-6">
                <h3 class="font-bold mb-4">Productos</h3>
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr><th class="py-2">Producto</th><th class="py-2">Cantidad</th><th class="py-2">Precio</th><th class="py-2">Total</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->product_name }}</td>
                                <td class="py-2">{{ $item->quantity }}</td>
                                <td class="py-2">S/ {{ number_format($item->unit_price,2) }}</td>
                                <td class="py-2 font-semibold">S/ {{ number_format($item->total,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-right mt-4 space-y-1 text-sm">
                    <div>Subtotal: <strong>S/ {{ number_format($order->subtotal,2) }}</strong></div>
                    <div>Envío: <strong>S/ {{ number_format($order->shipping_cost,2) }}</strong></div>
                    @if($order->discount_amount > 0)
                        <div class="text-green-600">Descuento: <strong>-S/ {{ number_format($order->discount_amount,2) }}</strong></div>
                    @endif
                    <div class="text-lg">Total: <strong class="text-rar-600">S/ {{ number_format($order->total,2) }}</strong></div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border rounded-xl p-6">
                <h3 class="font-bold mb-3">Dirección de envío</h3>
                <p class="text-sm">{{ $order->shipping_address }}</p>
                @if($order->shipping_city)<p class="text-sm text-gray-500">{{ $order->shipping_city }}</p>@endif
                <p class="text-sm text-gray-500">{{ $order->shipping_phone }}</p>
                @if($order->notes)<p class="text-sm mt-2"><strong>Notas:</strong> {{ $order->notes }}</p>@endif
            </div>

            <div class="bg-white border rounded-xl p-6">
                <h3 class="font-bold mb-3">Resumen</h3>
                <div class="text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-gray-500">Estado</span><span class="font-medium">{{ $order->statusLabel() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Método de pago</span><span class="font-medium">{{ ucfirst($order->payment_method) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Fecha</span><span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
