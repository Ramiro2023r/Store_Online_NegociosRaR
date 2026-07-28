@extends('layouts.app')
@section('title', 'Comparar productos - Negocios RaR')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">📊 Comparar productos</h1>
        @if($products->isNotEmpty())
            <form action="{{ route('compare.clear') }}" method="POST" onsubmit="return confirm('¿Limpiar la comparación?')">
                @csrf
                <button class="text-sm text-red-600 hover:underline">Limpiar comparación</button>
            </form>
        @endif
    </div>

    @if($products->isEmpty())
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🔍</div>
            <p class="text-gray-500 mb-4">No hay productos para comparar.</p>
            <a href="{{ route('products.index') }}" class="bg-rar-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-rar-700">Ver productos</a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr>
                        <th class="p-3 bg-gray-50 text-left w-48 min-w-[180px] border"></th>
                        @foreach($products as $product)
                            <th class="p-3 bg-gray-50 text-center border min-w-[200px]">
                                <form action="{{ route('compare.toggle', $product) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button class="text-xs text-red-500 hover:underline">&times; Quitar</button>
                                </form>
                                <a href="{{ route('products.show', $product) }}">
                                    @if($product->main_image)
                                        <img src="{{ asset('storage/'.$product->main_image) }}" class="h-32 w-32 object-cover rounded-lg mx-auto">
                                    @else
                                        <div class="h-32 w-32 mx-auto flex items-center justify-center bg-gray-100 rounded-lg text-4xl">🛍️</div>
                                    @endif
                                    <p class="font-semibold mt-2 hover:text-rar-600">{{ $product->name }}</p>
                                </a>
                                <div class="mt-1">
                                    @if($product->hasDiscount())
                                        <span class="text-xs text-gray-400 line-through">S/ {{ number_format($product->compare_price,2) }}</span>
                                    @endif
                                    <div class="text-rar-600 font-bold">S/ {{ number_format($product->price,2) }}</div>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-3 border font-medium bg-gray-50">Marca</td>
                        @foreach($products as $product)
                            <td class="p-3 border text-center">{{ $product->brand ?? '—' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="p-3 border font-medium bg-gray-50">Categoría</td>
                        @foreach($products as $product)
                            <td class="p-3 border text-center">{{ $product->category->name ?? '—' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="p-3 border font-medium bg-gray-50">Disponibilidad</td>
                        @foreach($products as $product)
                            <td class="p-3 border text-center">
                                @if($product->stock > 0)
                                    <span class="text-green-600 font-medium">En stock ({{ $product->stock }})</span>
                                @else
                                    <span class="text-red-600 font-medium">Agotado</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="p-3 border font-medium bg-gray-50">SKU</td>
                        @foreach($products as $product)
                            <td class="p-3 border text-center">{{ $product->sku ?? '—' }}</td>
                        @endforeach
                    </tr>
                    @if($products->first()->rating)
                    <tr>
                        <td class="p-3 border font-medium bg-gray-50">Valoración</td>
                        @foreach($products as $product)
                            <td class="p-3 border text-center">
                                <span class="text-cobre-500">
                                    @for($i=1;$i<=5;$i++){{ $i <= round($product->averageRating()) ? '★' : '☆' }}@endfor
                                </span>
                                <span class="text-xs text-gray-400">({{ $product->averageRating() }})</span>
                            </td>
                        @endforeach
                    </tr>
                    @endif
                    @foreach($attributes as $attrKey)
                        <tr>
                            <td class="p-3 border font-medium bg-gray-50 capitalize">{{ str_replace('_',' ',$attrKey) }}</td>
                            @foreach($products as $product)
                                @php $val = $product->attributes[$attrKey] ?? '—'; @endphp
                                <td class="p-3 border text-center">{{ is_array($val) ? implode(', ', $val) : $val }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td class="p-3 border font-medium bg-gray-50"></td>
                        @foreach($products as $product)
                            <td class="p-3 border text-center">
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="bg-rar-600 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-rar-700">🛒 Agregar al carrito</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Agotado</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
