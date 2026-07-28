@extends('layouts.app')
@section('title', 'Carrito de compras - Negocios RaR')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">🛒 Carrito de compras</h1>

    @if($cart->items->isEmpty())
        <div class="text-center py-20 bg-white border rounded-xl">
            <div class="text-6xl mb-4">🛒</div>
            <p class="text-gray-500 mb-4">Tu carrito está vacío.</p>
            <a href="{{ route('products.index') }}" class="bg-rar-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-rar-700">Ver productos</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-3">
                @foreach($cart->items as $item)
                    <div class="bg-white border rounded-xl p-4 flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                            @if($item->product->main_image)
                                <img src="{{ asset('storage/'.$item->product->main_image) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl">🛍️</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <a href="{{ route('products.show', $item->product) }}" class="font-semibold text-sm hover:text-rar-600">{{ $item->product->name }}</a>
                            @if($item->variant)
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $item->variant->size ?? '' }} {{ $item->variant->color ?? '' }}
                                </div>
                            @endif
                            <div class="text-rar-600 font-bold mt-1">S/ {{ number_format($item->unit_price,2) }}</div>
                        </div>
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-1">
                            @csrf @method('PATCH')
                            @php $maxStock = $item->variant?->stock ?? $item->product->stock; @endphp
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $maxStock }}" class="w-16 border rounded-lg px-2 py-1.5 text-center text-sm" onchange="this.form.submit()">
                        </form>
                        <div class="font-bold w-24 text-right">S/ {{ number_format($item->unit_price * $item->quantity, 2) }}</div>
                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-sm">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="bg-white border rounded-xl p-6 h-fit">
                <h3 class="font-bold mb-4">Resumen del pedido</h3>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-500">Subtotal</span>
                    <span>S/ {{ number_format($cart->total(),2) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-4">
                    <span class="text-gray-500">Envío</span>
                    <span>{{ $cart->total() >= 200 ? 'Gratis' : 'S/ 15.00' }}</span>
                </div>
                <div class="border-t pt-4 flex justify-between font-bold text-lg mb-4">
                    <span>Total</span>
                    <span class="text-rar-600">S/ {{ number_format($cart->total() + ($cart->total() >= 200 ? 0 : 15), 2) }}</span>
                </div>
                @auth
                    <a href="{{ route('checkout.index') }}" class="block text-center bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">Proceder al pago</a>
                @else
                    <a href="{{ route('login') }}" class="block text-center bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">Inicia sesión para continuar</a>
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection
