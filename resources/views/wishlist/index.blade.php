@extends('layouts.app')
@section('title', 'Mi lista de deseos - Negocios RaR')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">❤️ Mi lista de deseos</h1>

    @if($wishlists->isEmpty())
        <div class="text-center py-16">
            <div class="text-7xl mb-4">💔</div>
            <h2 class="text-lg font-semibold mb-2">Tu lista de deseos está vacía</h2>
            <p class="text-sm text-gray-500 mb-6">Guarda productos que te interesen y vuelve a ellos cuando quieras.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-rar-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-rar-700">Ver productos</a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach($wishlists as $wishlist)
                <div class="bg-white border rounded-xl overflow-hidden hover:shadow-lg transition">
                    @include('partials.product-card', ['product' => $wishlist->product])
                    <div class="px-3 pb-3 -mt-2">
                        <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="mb-2">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline" onclick="return confirm('¿Quitar de tu lista de deseos?')">✕ Quitar</button>
                        </form>
                        @if($wishlist->product->stock > 0)
                            <form action="{{ route('cart.add', $wishlist->product) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button class="w-full bg-rar-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-rar-700">🛒 Agregar al carrito</button>
                            </form>
                        @else
                            <span class="block w-full text-center text-sm text-gray-400 py-2 border border-gray-200 rounded-lg">Agotado</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
