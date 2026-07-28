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
    <div>
        <label class="text-sm font-medium">Stock mínimo (alerta)</label>
        <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 5) }}" min="0" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Cant. sugerida de reabastecimiento</label>
        <input type="number" name="restock_quantity" value="{{ old('restock_quantity', $product->restock_quantity ?? 0) }}" min="0" class="w-full border rounded-lg px-3 py-2 mt-1">
        <p class="text-xs text-gray-400 mt-1">0 = sin sugerencia</p>
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
    <div>
        <label class="text-sm font-medium">Video del producto (opcional)</label>
        <input type="text" name="video_url" value="{{ old('video_url', $product->video_url ?? '') }}" class="w-full border rounded-lg px-3 py-2 mt-1" placeholder="YouTube: https://www.youtube.com/watch?v=... o enlace directo .mp4">
        <p class="text-xs text-gray-400 mt-1">Soporta YouTube, Vimeo o URL directa de video MP4</p>
    </div>
    <div class="col-span-2">
        <label class="text-sm font-medium">Meta título (SEO, 70 caracteres máx)</label>
        <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title ?? '') }}" maxlength="70" class="w-full border rounded-lg px-3 py-2 mt-1" placeholder="Dejar vacío para usar el nombre del producto">
    </div>
    <div class="col-span-2">
        <label class="text-sm font-medium">Meta descripción (SEO, 160 caracteres máx)</label>
        <textarea name="meta_description" rows="2" maxlength="160" class="w-full border rounded-lg px-3 py-2 mt-1" placeholder="Dejar vacío para auto-generar desde la descripción">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
    </div>
    <div class="col-span-2 flex gap-6">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured ?? false) ? 'checked' : '' }}> Producto destacado (aparece en inicio)
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="active" value="1" {{ old('active', $product->active ?? true) ? 'checked' : '' }}> Producto activo (visible en tienda)
        </label>
    </div>
    @if(isset($product) && $product->exists)
        <div class="col-span-2 border-t pt-4 mt-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-sm">📦 Variantes de producto</p>
                    <p class="text-xs text-gray-400">Gestiona combinaciones de talla y color con su propio stock y precio.</p>
                </div>
                <a href="{{ route('admin.products.variants.index', $product) }}" class="bg-rar-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rar-700">Gestionar variantes</a>
            </div>
        </div>
    @endif
</div>
