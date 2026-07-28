@extends('layouts.app')
@section('title', 'Productos - Negocios RaR')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Todos los productos</h1>

    <div class="flex flex-col md:flex-row gap-8">
        {{-- Filtros --}}
        <aside class="w-full md:w-64 shrink-0">
            <form method="GET" action="{{ route('products.index') }}" class="bg-white border rounded-xl p-4 space-y-5 sticky top-20">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar..." class="w-full border rounded-lg px-3 py-2 text-sm">

                <div>
                    <h4 class="font-semibold text-sm mb-2">Categoría</h4>
                    <div class="space-y-1 text-sm">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }}> Todas
                        </label>
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }}>
                                {{ $cat->icon }} {{ $cat->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-sm mb-2">Marca</h4>
                    <select name="brand" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">Todas las marcas</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <h4 class="font-semibold text-sm mb-2">Precio (S/)</h4>
                    <div class="flex gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Mín" class="w-1/2 border rounded-lg px-2 py-2 text-sm">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Máx" class="w-1/2 border rounded-lg px-2 py-2 text-sm">
                    </div>
                </div>

                <button class="w-full bg-rar-600 text-white font-semibold py-2 rounded-lg text-sm hover:bg-rar-700">Aplicar filtros</button>
                <a href="{{ route('products.index') }}" class="block text-center text-xs text-gray-500 hover:underline">Limpiar filtros</a>
            </form>
        </aside>

        {{-- Resultados --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">{{ $products->total() }} productos encontrados</p>
                <form method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <label class="text-sm text-gray-500">Ordenar por:</label>
                    <select name="sort" onchange="this.form.submit()" class="border rounded-lg px-2 py-1.5 text-sm">
                        <option value="">Más recientes</option>
                        <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Precio: menor a mayor</option>
                        <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Precio: mayor a menor</option>
                        <option value="name" {{ request('sort')=='name'?'selected':'' }}>Nombre A-Z</option>
                    </select>
                </form>
            </div>

            @if($products->count())
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @else
                <div class="text-center py-16 text-gray-400">
                    <div class="text-5xl mb-3">🔍</div>
                    No encontramos productos con esos filtros.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
