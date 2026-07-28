@extends('layouts.app')
@section('title', $product->name.' - Negocios RaR')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-rar-600">Inicio</a> /
        <a href="{{ route('products.index') }}" class="hover:text-rar-600">Productos</a> /
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-rar-600">{{ $product->category->name }}</a> /
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div>
            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
                @if($product->main_image)
                    <img src="{{ asset('storage/'.$product->main_image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                @else
                    <span class="text-8xl">🛍️</span>
                @endif
            </div>
            @if($product->images->count())
                <div class="grid grid-cols-4 gap-2 mt-3">
                    @foreach($product->images as $img)
                        <img src="{{ asset('storage/'.$img->path) }}" class="aspect-square object-cover rounded-lg border">
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="text-xs font-semibold text-rar-600 uppercase">{{ $product->category->name }} &middot; {{ $product->brand }}</div>
            <h1 class="text-2xl md:text-3xl font-bold mt-1">{{ $product->name }}</h1>

            <div class="flex items-center gap-2 mt-2">
                <div class="flex text-cobre-500">
                    @for($i=1;$i<=5;$i++){{ $i <= round($product->averageRating()) ? '★' : '☆' }}@endfor
                </div>
                <span class="text-sm text-gray-400">({{ $product->averageRating() }}) {{ $product->reviewsCount() > 0 ? '· '.$product->reviewsCount().' reseña(s)' : '' }}</span>
            </div>

            <div class="mt-4 flex items-center gap-3">
                @if($product->hasDiscount())
                    <span class="text-gray-400 line-through">S/ {{ number_format($product->compare_price,2) }}</span>
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded">-{{ $product->discountPercent() }}%</span>
                @endif
            </div>
            <div class="text-3xl font-bold text-rar-600 mt-1">S/ {{ number_format($product->price,2) }}</div>

            <p class="text-sm text-gray-600 mt-4 leading-relaxed">{{ $product->description }}</p>

            @php $attrs = is_array($product->attributes) ? $product->attributes : json_decode($product->attributes, true) ?? []; @endphp
            @if(!empty($attrs))
                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    @foreach($attrs as $key => $value)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <span class="text-gray-400 capitalize">{{ str_replace('_',' ',$key) }}:</span> <span class="font-medium">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4 text-sm">
                @if($product->stock > 5)
                    <span class="text-green-600 font-medium">✓ En stock ({{ $product->stock }} disponibles)</span>
                @elseif($product->stock > 0)
                    <span class="text-cobre-600 font-medium">⚠ Últimas {{ $product->stock }} unidades</span>
                @else
                    <span class="text-red-600 font-medium">✗ Agotado</span>
                @endif
            </div>

            @if($product->stock > 0)
                <div class="mt-6 flex items-center gap-3">
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1 flex items-center gap-3">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-20 border rounded-lg px-3 py-2 text-center">
                        <button class="flex-1 bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">🛒 Agregar al carrito</button>
                    </form>
                    @php $inWishlist = auth()->check() && auth()->user()->hasInWishlist($product); @endphp
                    <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-12 h-12 flex items-center justify-center rounded-lg border hover:bg-gray-50 transition" aria-label="{{ $inWishlist ? 'Quitar de lista de deseos' : 'Agregar a lista de deseos' }}">
                            <svg class="w-6 h-6 text-rar-600" viewBox="0 0 24 24" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Sección de reseñas --}}
    <div class="mt-16">
        <h2 class="text-xl font-bold mb-6">Reseñas de clientes</h2>

        @auth
            @php
                $userReview = $reviews->firstWhere('user_id', auth()->id());
            @endphp
            @if(!$userReview)
                <div class="bg-gray-50 border rounded-xl p-6 mb-8">
                    <h3 class="font-semibold mb-3">Escribe tu reseña</h3>
                    <form action="{{ route('reviews.store', $product) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Calificación</label>
                            <div class="flex gap-2 text-2xl text-cobre-500" x-data="{ rating: 0 }">
                                <template x-for="i in 5" :key="i">
                                    <button type="button" @click="rating = i" class="hover:scale-110 transition" x-html="i <= rating ? '★' : '☆'"></button>
                                </template>
                                <input type="hidden" name="rating" x-model="rating" :value="rating">
                            </div>
                            @error('rating') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="block text-sm font-medium mb-1">Comentario <span class="text-gray-400">(opcional)</span></label>
                            <textarea name="comment" id="comment" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" maxlength="2000" placeholder="Comparte tu experiencia con este producto...">{{ old('comment') }}</textarea>
                            @error('comment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button class="bg-rar-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-rar-700 text-sm">Enviar reseña</button>
                    </form>
                </div>
            @else
                <p class="text-sm text-gray-500 mb-6">Ya has escrito una reseña para este producto.</p>
            @endif
        @else
            <div class="bg-gray-50 border rounded-xl p-6 mb-8 text-center">
                <p class="text-sm text-gray-500">Para escribir una reseña, <a href="{{ route('login') }}" class="text-rar-600 font-semibold hover:underline">inicia sesión</a> o <a href="{{ route('register') }}" class="text-rar-600 font-semibold hover:underline">regístrate</a>.</p>
            </div>
        @endauth

        @forelse($reviews as $review)
            <div class="border-b pb-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-rar-100 flex items-center justify-center text-sm font-bold text-rar-600">{{ substr($review->user->name, 0, 1) }}</div>
                        <span class="font-medium text-sm">{{ $review->user->name }}</span>
                    </div>
                    <div class="flex text-cobre-500 text-sm">
                        @for($i=1;$i<=5;$i++)
                            <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                </div>
                @if($review->comment)
                    <p class="text-sm text-gray-600 mt-2">{{ $review->comment }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                @auth
                    @if($review->user_id === auth()->id())
                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="mt-1" onsubmit="return confirm('¿Eliminar tu reseña?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Eliminar</button>
                        </form>
                    @endif
                @endauth
            </div>
        @empty
            <p class="text-sm text-gray-400">Aún no hay reseñas para este producto. ¡Sé el primero en opinar!</p>
        @endforelse
    </div>

    @if($related->count())
        <div class="mt-16">
            <h2 class="text-xl font-bold mb-6">Productos relacionados</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                @foreach($related as $rp)
                    @include('partials.product-card', ['product' => $rp])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
