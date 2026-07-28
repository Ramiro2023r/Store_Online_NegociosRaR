@extends('layouts.app')
@php
    $metaTitle = $product->meta_title ?: $product->name . ' - Negocios RaR';
    $metaDesc = $product->meta_description ?: Str::limit(strip_tags($product->description), 160);
    $metaImage = $product->main_image ? asset('storage/' . $product->main_image) : asset('images/Mejoradelogoiconoapp.svg');
    $productUrl = route('products.show', $product);
    $avgRating = $product->averageRating();
    $reviewsCount = $product->reviewsCount();
@endphp
@push('meta')
    <meta name="description" content="{{ $metaDesc }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:url" content="{{ $productUrl }}">
    <meta property="og:type" content="product">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ $product->name }}",
        "description": "{{ $metaDesc }}",
        "sku": "{{ $product->sku }}",
        "brand": { "@type": "Brand", "name": "{{ $product->brand ?? 'Negocios RaR' }}" },
        "image": "{{ $metaImage }}",
        "offers": {
            "@type": "Offer",
            "price": "{{ $product->price }}",
            "priceCurrency": "PEN",
            "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "url": "{{ $productUrl }}"
        }@if($reviewsCount > 0),
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $avgRating }}",
            "reviewCount": "{{ $reviewsCount }}"
        }@endif
    }
    </script>
@endpush
@section('title', $metaTitle)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-rar-600">Inicio</a> /
        <a href="{{ route('products.index') }}" class="hover:text-rar-600">Productos</a> /
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-rar-600">{{ $product->category->name }}</a> /
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    @php
        $hasVideo = $product->video_url;
        $videoId = null; $videoPlatform = null;
        if ($hasVideo) {
            if (preg_match('/youtube\.com\/watch\?v=([\w-]+)/', $product->video_url, $m) || preg_match('/youtu\.be\/([\w-]+)/', $product->video_url, $m)) {
                $videoId = $m[1]; $videoPlatform = 'youtube';
            } elseif (preg_match('/vimeo\.com\/(\d+)/', $product->video_url, $m)) {
                $videoId = $m[1]; $videoPlatform = 'vimeo';
            }
        }
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div>
            <div class="relative" x-data="imageGallery({{ Js::from(['main' => $product->main_image ? asset('storage/'.$product->main_image) : null, 'gallery' => $product->images->map(fn($i) => asset('storage/'.$i->path))]) }})">
                {{-- Main image with zoom --}}
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center relative cursor-crosshair"
                     x-ref="imageContainer"
                     @mouseenter="startZoom"
                     @mousemove="moveZoom"
                     @mouseleave="stopZoom">
                    <template x-if="currentSrc">
                        <img :src="currentSrc" class="w-full h-full object-cover select-none" draggable="false"
                             style="image-rendering: auto;"
                             :style="zoomStyles">
                    </template>
                    <template x-if="!currentSrc">
                        <span class="text-8xl">🛍️</span>
                    </template>
                    {{-- Zoom lens --}}
                    <div x-show="zooming" x-cloak
                         class="absolute inset-0 pointer-events-none"
                         :style="lensStyles">
                    </div>
                </div>

                {{-- Zoomed result (right side on desktop) --}}
                <div x-show="zooming" x-cloak
                     class="hidden md:block absolute top-0 left-[calc(100%+1rem)] w-full h-full border rounded-xl overflow-hidden bg-white z-10 shadow-xl"
                     x-ref="zoomResult">
                    <div class="w-full h-full"
                         :style="zoomResultStyles">
                    </div>
                </div>

                {{-- Thumbnails + video --}}
                <div class="flex gap-2 mt-3 overflow-x-auto pb-1" x-ref="thumbnails">
                    {{-- Main image thumbnail --}}
                    @if($product->main_image)
                        <button @click="select('main')"
                                class="shrink-0 w-16 h-16 rounded-lg border-2 overflow-hidden focus:outline-none"
                                :class="selected === 'main' ? 'border-rar-600' : 'border-gray-200'">
                            <img src="{{ asset('storage/'.$product->main_image) }}" class="w-full h-full object-cover">
                        </button>
                    @endif
                    {{-- Gallery images --}}
                    @foreach($product->images as $i => $img)
                        <button @click="select('gallery', {{ $i }})"
                                class="shrink-0 w-16 h-16 rounded-lg border-2 overflow-hidden focus:outline-none"
                                :class="selected === 'gallery-' + {{ $i }} ? 'border-rar-600' : 'border-gray-200'">
                            <img src="{{ asset('storage/'.$img->path) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                    {{-- Video thumbnail --}}
                    @if($videoId && $videoPlatform === 'youtube')
                        <button @click="select('video')"
                                class="shrink-0 w-16 h-16 rounded-lg border-2 overflow-hidden focus:outline-none relative flex items-center justify-center bg-black"
                                :class="selected === 'video' ? 'border-rar-600' : 'border-gray-200'">
                            <img src="https://img.youtube.com/vi/{{ $videoId }}/default.jpg" class="w-full h-full object-cover opacity-80">
                            <svg class="absolute w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                        </button>
                    @elseif($videoId && $videoPlatform === 'vimeo')
                        <button @click="select('video')"
                                class="shrink-0 w-16 h-16 rounded-lg border-2 overflow-hidden focus:outline-none relative flex items-center justify-center bg-gray-900"
                                :class="selected === 'video' ? 'border-rar-600' : 'border-gray-200'">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M22.396 7.164c-.093 2.026-1.507 4.799-4.245 8.32C15.322 19.18 12.94 21 10.97 21c-1.214 0-2.24-1.119-3.079-3.358-.56-2.052-1.12-4.104-1.68-6.157-.622-2.238-1.29-3.357-2.005-3.357-.153 0-.685.322-1.603.966l-.988-1.276c1.003-.88 1.992-1.757 2.967-2.632 1.336-1.152 2.338-1.76 3.006-1.82 1.58-.153 2.555.93 2.925 3.243.396 2.498.67 4.048.823 4.652.457 2.074.96 3.111 1.508 3.111.428 0 1.07-.674 1.928-2.025.857-1.35 1.315-2.38 1.374-3.085.122-1.168-.337-1.756-1.374-1.756-.49 0-.995.111-1.514.33.503-1.648 1.464-2.448 2.882-2.4 1.052.034 1.85.398 2.396 1.092.547.694.842 1.644.738 2.85z"/></svg>
                        </button>
                    @endif
                </div>

                {{-- Video embed (shown when video selected) --}}
                <div x-show="selected === 'video'" x-cloak class="absolute inset-0 z-20 bg-black rounded-xl overflow-hidden flex items-center justify-center">
                    @if($videoPlatform === 'youtube')
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    @elseif($videoPlatform === 'vimeo')
                        <iframe class="w-full h-full" src="https://player.vimeo.com/video/{{ $videoId }}?autoplay=1" frameborder="0" allow="autoplay" allowfullscreen></iframe>
                    @else
                        <video class="w-full h-full" controls autoplay>
                            <source src="{{ $product->video_url }}" type="video/mp4">
                        </video>
                    @endif
                    <button @click="selected = 'main'" class="absolute top-2 right-2 bg-black/60 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-black/80">✕</button>
                </div>
            </div>

            <script>
                function imageGallery(images) {
                    return {
                        currentSrc: images.main || null,
                        selected: 'main',
                        zooming: false,
                        zoomLevel: 2.5,
                        lensSize: 120,
                        lensX: 0,
                        lensY: 0,
                        bgX: 0,
                        bgY: 0,
                        get zoomStyles() {
                            if (!this.zooming) return {};
                            return {
                                transform: `scale(${this.zoomLevel})`,
                                transformOrigin: `${this.bgX}% ${this.bgY}%`,
                            };
                        },
                        get lensStyles() {
                            if (!this.zooming) return {};
                            const s = this.lensSize;
                            return {
                                width: s + 'px',
                                height: s + 'px',
                                left: (this.lensX - s/2) + 'px',
                                top: (this.lensY - s/2) + 'px',
                                background: 'rgba(255,255,255,0.3)',
                                border: '2px solid rgba(0,0,0,0.2)',
                                borderRadius: '50%',
                            };
                        },
                        get zoomResultStyles() {
                            if (!this.currentSrc || !this.zooming) return {};
                            return {
                                backgroundImage: `url('${this.currentSrc}')`,
                                backgroundSize: `${this.zoomLevel * 100}%`,
                                backgroundPosition: `${this.bgX}% ${this.bgY}%`,
                                backgroundRepeat: 'no-repeat',
                            };
                        },
                        select(type, index) {
                            if (type === 'video') {
                                this.selected = 'video';
                                this.zooming = false;
                                return;
                            }
                            if (type === 'main') {
                                this.currentSrc = images.main;
                                this.selected = 'main';
                            } else if (type === 'gallery') {
                                this.currentSrc = images.gallery[index];
                                this.selected = 'gallery-' + index;
                            }
                        },
                        startZoom(e) {
                            this.zooming = true;
                            this.updateZoom(e);
                        },
                        moveZoom(e) {
                            if (!this.zooming) return;
                            this.updateZoom(e);
                        },
                        stopZoom() {
                            this.zooming = false;
                        },
                        updateZoom(e) {
                            const rect = this.$refs.imageContainer.getBoundingClientRect();
                            const x = (e.clientX - rect.left) / rect.width * 100;
                            const y = (e.clientY - rect.top) / rect.height * 100;
                            this.bgX = Math.min(100, Math.max(0, x));
                            this.bgY = Math.min(100, Math.max(0, y));
                            this.lensX = (e.clientX - rect.left);
                            this.lensY = (e.clientY - rect.top);
                        }
                    };
                }
            </script>
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
                    <span class="text-gray-400 line-through" id="compare-price">S/ {{ number_format($product->compare_price,2) }}</span>
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded">-{{ $product->discountPercent() }}%</span>
                @else
                    <span class="text-gray-400 line-through hidden" id="compare-price"></span>
                @endif
            </div>
            <div class="text-3xl font-bold text-rar-600 mt-1" id="product-price">S/ {{ number_format($product->price,2) }}</div>

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

            <div class="mt-4 text-sm" id="stock-display">
                @php
                    $hasVariants = $product->hasVariants();
                    $baseStock = $hasVariants ? $product->activeVariants->sum('stock') : $product->stock;
                @endphp
                @if($baseStock > $product->min_stock)
                    <span class="text-green-600 font-medium" id="stock-text">✓ En stock ({{ $baseStock }} disponibles)</span>
                @elseif($baseStock > 0)
                    <span class="text-cobre-600 font-medium" id="stock-text">⚠ Últimas {{ $baseStock }} unidades</span>
                @else
                    <span class="text-red-600 font-medium" id="stock-text">✗ Agotado</span>
                @endif
            </div>

            @if($baseStock > 0)
                <div class="mt-6 flex items-center gap-3">
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1" id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="variant_id" id="selected-variant" value="">

                        @if($hasVariants)
                            @php
                                $sizes = $product->activeVariants->pluck('size')->unique()->filter()->values();
                                $colors = $product->activeVariants->pluck('color')->unique()->filter()->values();
                                $variantsJson = $product->activeVariants->map(fn($v) => [
                                    'id' => $v->id,
                                    'size' => $v->size,
                                    'color' => $v->color,
                                    'stock' => $v->stock,
                                    'price' => $v->price ? (float) $v->price : null,
                                    'compare_price' => $v->compare_price ? (float) $v->compare_price : null,
                                    'image' => $v->image ? asset('storage/'.$v->image) : null,
                                ]);
                            @endphp
                            <div class="flex gap-4 mb-4" x-data="variantSelector({{ $variantsJson }}, '{{ $product->price }}', '{{ $product->compare_price ?? '' }}', '{{ $product->stock }}', '{{ $product->min_stock }}')">
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Talla</label>
                                    <select x-model="selectedSize" @change="updateVariant" class="border rounded-lg px-3 py-2 text-sm mt-1">
                                        <option value="">Seleccionar</option>
                                        @foreach($sizes as $s)
                                            <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Color</label>
                                    <select x-model="selectedColor" @change="updateVariant" class="border rounded-lg px-3 py-2 text-sm mt-1">
                                        <option value="">Seleccionar</option>
                                        @foreach($colors as $c)
                                            <option value="{{ $c }}">{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <script>
                                function variantSelector(variants, basePrice, baseCompare, baseStock, minStock) {
                                    return {
                                        variants: variants,
                                        selectedSize: '',
                                        selectedColor: '',
                                        currentVariant: null,
                                        get stockDisplay() {
                                            return this.currentVariant ? this.currentVariant.stock : parseInt(baseStock);
                                        },
                                        init() {
                                            this.updateVariant();
                                        },
                                        updateVariant() {
                                            const found = this.variants.find(v =>
                                                (this.selectedSize === '' || v.size === this.selectedSize) &&
                                                (this.selectedColor === '' || v.color === this.selectedColor) &&
                                                (v.size === this.selectedSize || v.color === this.selectedColor)
                                            );
                                            this.currentVariant = found || null;

                                            const priceEl = document.getElementById('product-price');
                                            const compareEl = document.getElementById('compare-price');
                                            const stockEl = document.getElementById('stock-text');
                                            const stockContainer = document.getElementById('stock-display');
                                            const variantInput = document.getElementById('selected-variant');
                                            const qtyInput = document.querySelector('input[name="quantity"]');

                                            if (found) {
                                                variantInput.value = found.id;
                                                if (found.price) {
                                                    priceEl.textContent = 'S/ ' + found.price.toFixed(2);
                                                    if (found.compare_price) {
                                                        compareEl.innerHTML = 'S/ ' + found.compare_price.toFixed(2);
                                                        compareEl.classList.remove('hidden');
                                                    } else {
                                                        compareEl.classList.add('hidden');
                                                    }
                                                } else {
                                                    priceEl.textContent = 'S/ ' + parseFloat(basePrice).toFixed(2);
                                                    if (baseCompare) {
                                                        compareEl.innerHTML = 'S/ ' + parseFloat(baseCompare).toFixed(2);
                                                        compareEl.classList.remove('hidden');
                                                    } else {
                                                        compareEl.classList.add('hidden');
                                                    }
                                                }
                                                if (found.stock > parseInt(minStock)) {
                                                    stockEl.innerHTML = '✓ En stock (' + found.stock + ' disponibles)';
                                                    stockEl.className = 'text-green-600 font-medium';
                                                } else if (found.stock > 0) {
                                                    stockEl.innerHTML = '⚠ Últimas ' + found.stock + ' unidades';
                                                    stockEl.className = 'text-cobre-600 font-medium';
                                                } else {
                                                    stockEl.innerHTML = '✗ Agotado';
                                                    stockEl.className = 'text-red-600 font-medium';
                                                }
                                                qtyInput.max = found.stock;
                                            } else {
                                                variantInput.value = '';
                                                priceEl.textContent = 'S/ ' + parseFloat(basePrice).toFixed(2);
                                                if (baseCompare) {
                                                    compareEl.innerHTML = 'S/ ' + parseFloat(baseCompare).toFixed(2);
                                                    compareEl.classList.remove('hidden');
                                                }
                                                stockEl.innerHTML = '✓ En stock';
                                                qtyInput.max = baseStock;
                                            }
                                        }
                                    }
                                }
                            </script>
                        @else
                            <input type="hidden" name="variant_id" value="">
                        @endif

                        <div class="flex items-center gap-3">
                            <input type="number" name="quantity" value="1" min="1"
                                max="{{ $hasVariants ? $product->activeVariants->max('stock') ?: $product->stock : $product->stock }}"
                                class="w-20 border rounded-lg px-3 py-2 text-center">
                            <button type="submit" class="flex-1 bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">🛒 Agregar al carrito</button>
                        </div>
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
                    <form action="{{ route('compare.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-12 h-12 flex items-center justify-center rounded-lg border hover:bg-gray-50 transition" title="Comparar">
                            <svg class="w-6 h-6 text-rar-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        </button>
                    </form>
                </div>

                <div x-data="{ showShare: false }" class="mt-3">
                    <button @click="showShare = !showShare" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        Compartir
                    </button>
                    <div x-show="showShare" @click.outside="showShare = false" class="flex items-center gap-2 mt-2 text-sm" x-cloak>
                        <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . $productUrl) }}" target="_blank" class="text-green-500 hover:text-green-600" title="WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($productUrl) }}" target="_blank" class="text-[#1877F2] hover:text-blue-700" title="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode($productUrl) }}" target="_blank" class="text-gray-500 hover:text-black" title="X (Twitter)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    </div>
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
