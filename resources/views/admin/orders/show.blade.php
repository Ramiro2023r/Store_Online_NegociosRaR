@extends('layouts.admin')
@section('title', 'Pedido - Admin')
@section('page-title', '🧾 Pedido #' . $order->order_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white border rounded-xl p-6">
        <h3 class="font-bold mb-4">Productos del pedido</h3>
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
            <div class="text-lg">Total: <strong class="text-rar-600">S/ {{ number_format($order->total,2) }}</strong></div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-6">
            <h3 class="font-bold mb-3">Cliente</h3>
            <p class="text-sm">{{ $order->user->name }}</p>
            <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
            <p class="text-sm text-gray-500">{{ $order->shipping_phone }}</p>
            <hr class="my-3">
            <p class="text-sm"><strong>Envío a:</strong> {{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
            @if($order->notes)<p class="text-sm mt-2"><strong>Notas:</strong> {{ $order->notes }}</p>@endif
        </div>

        <div class="bg-white border rounded-xl p-6">
            <h3 class="font-bold mb-3">Actualizar estado</h3>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                    @foreach(['pendiente','pagado','enviado','entregado','cancelado'] as $s)
                        <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-rar-600 text-white font-semibold py-2 rounded-lg text-sm hover:bg-rar-700">Actualizar</button>
            </form>
        </div>
    </div>
</div>
@endsection
