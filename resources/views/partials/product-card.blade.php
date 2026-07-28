<a href="{{ route('products.show', $product) }}" class="bg-white border rounded-xl overflow-hidden hover:shadow-lg transition group">
    <div class="relative aspect-square bg-gray-100 overflow-hidden">
        @if($product->main_image)
            <img src="{{ asset('storage/'.$product->main_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition" alt="{{ $product->name }}">
        @else
            <div class="w-full h-full flex items-center justify-center text-5xl bg-gradient-to-br from-rar-50 to-rar-100">🛍️</div>
        @endif
        @if($product->hasDiscount())
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">-{{ $product->discountPercent() }}%</span>
        @endif
        @php $inWishlist = auth()->check() && auth()->user()->hasInWishlist($product); @endphp
        <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute top-2 right-2 z-10" @click.stop>
            @csrf
            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-white shadow-sm transition" aria-label="{{ $inWishlist ? 'Quitar de lista de deseos' : 'Agregar a lista de deseos' }}">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                </svg>
            </button>
        </form>
        @if($product->stock <= 5 && $product->stock > 0)
            <span class="absolute top-12 right-2 bg-cobre-500 text-white text-xs font-bold px-2 py-1 rounded">¡Últimas unidades!</span>
        @elseif($product->stock == 0)
            <span class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-sm font-bold">Agotado</span>
        @endif
    </div>
    <div class="p-3">
        <form action="{{ route('compare.toggle', $product) }}" method="POST" class="mb-1" @click.stop>
            @csrf
            <button class="text-xs text-rar-600 hover:underline flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                Comparar
            </button>
        </form>
        <div class="text-xs text-gray-400">{{ $product->brand }}</div>
        <h3 class="text-sm font-semibold line-clamp-2 h-10">{{ $product->name }}</h3>
        @php
            $avgRating = $product->reviews_avg_rating ?? $product->averageRating();
            $countReviews = $product->reviews_count ?? $product->reviewsCount();
        @endphp
        <div class="flex items-center gap-1 mt-1 text-cobre-500 text-xs">
            @for($i=1;$i<=5;$i++)
                <span>{{ $i <= round($avgRating) ? '★' : '☆' }}</span>
            @endfor
            @if($countReviews > 0)
                <span class="text-gray-400 ml-1">({{ $countReviews }})</span>
            @endif
        </div>
        <div class="mt-2">
            @if($product->hasDiscount())
                <span class="text-xs text-gray-400 line-through">S/ {{ number_format($product->compare_price, 2) }}</span>
            @endif
            <div class="text-rar-600 font-bold">S/ {{ number_format($product->price, 2) }}</div>
        </div>
    </div>
</a>
