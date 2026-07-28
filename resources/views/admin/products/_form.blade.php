@php($product = $product ?? null)

<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <label class="text-sm font-medium">Nombre del producto</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Categoría</label>
        <select name="category_id" required class="w-full border rounded-lg px-3 py-2 mt-1">
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Marca</label>
        <input type="text" name="brand" value="{{ old('brand', $product->brand ?? '') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Precio (S/)</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Precio anterior / tachado (opcional)</label>
        <input type="number" step="0.01" name="compare_price" value="{{ old('compare_price', $product->compare_price ?? '') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">SKU (opcional)</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Stock disponible</label>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div class="col-span-2">
        <label class="text-sm font-medium">Descripción</label>
        <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div>
        <label class="text-sm font-medium">Imagen principal</label>
        <input type="file" name="main_image" accept="image/*" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Galería de imágenes (opcional)</label>
        <input type="file" name="gallery[]" accept="image/*" multiple class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div class="col-span-2 flex gap-6">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured ?? false) ? 'checked' : '' }}> Producto destacado (aparece en inicio)
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="active" value="1" {{ old('active', $product->active ?? true) ? 'checked' : '' }}> Producto activo (visible en tienda)
        </label>
    </div>
</div>
