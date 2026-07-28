@extends('layouts.admin')
@section('title', 'Editar variante - ' . $variant->size . '/' . $variant->color)
@section('page-title', '✏️ Editar variante')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.products.variants.index', $product) }}" class="text-sm text-rar-600 hover:underline">← Volver a variantes</a>
</div>

<div class="bg-white border rounded-xl p-6 max-w-lg">
    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" enctype="multipart/form-data" class="grid gap-4">
        @csrf @method('PUT')
        <div>
            <label class="text-sm font-medium">Talla</label>
            <input type="text" name="size" value="{{ old('size', $variant->size) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Color</label>
            <input type="text" name="color" value="{{ old('color', $variant->color) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $variant->sku) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Stock</label>
            <input type="number" name="stock" value="{{ old('stock', $variant->stock) }}" min="0" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Precio (dejar vacío para usar el del producto)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $variant->price) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Precio tachado</label>
            <input type="number" step="0.01" name="compare_price" value="{{ old('compare_price', $variant->compare_price) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Imagen</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2 mt-1">
            @if($variant->image)
                <p class="text-xs text-gray-400 mt-1">Imagen actual: {{ $variant->image }}</p>
            @endif
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="active" value="1" {{ old('active', $variant->active) ? 'checked' : '' }}>
            Variante activa
        </label>
        <button class="bg-rar-600 text-white font-semibold py-2 rounded-lg hover:bg-rar-700">Guardar cambios</button>
    </form>
</div>
@endsection
