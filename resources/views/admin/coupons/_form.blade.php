@php($coupon = $coupon ?? null)

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="text-sm font-medium">Código del cupón</label>
        <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required class="w-full border rounded-lg px-3 py-2 mt-1 uppercase" placeholder="EJ. VERANO20" oninput="this.value = this.value.toUpperCase()">
    </div>
    <div>
        <label class="text-sm font-medium">Tipo</label>
        <select name="type" required class="w-full border rounded-lg px-3 py-2 mt-1">
            <option value="percentage" {{ old('type', $coupon->type ?? '') == 'percentage' ? 'selected' : '' }}>Porcentaje (%)</option>
            <option value="fixed" {{ old('type', $coupon->type ?? '') == 'fixed' ? 'selected' : '' }}>Monto fijo (S/)</option>
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Valor</label>
        <input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value ?? '') }}" required class="w-full border rounded-lg px-3 py-2 mt-1" placeholder="20.00">
    </div>
    <div>
        <label class="text-sm font-medium">Categoría (opcional)</label>
        <select name="category_id" class="w-full border rounded-lg px-3 py-2 mt-1">
            <option value="">Todas las categorías</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $coupon->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Compra mínima (S/)</label>
        <input type="number" step="0.01" name="min_purchase" value="{{ old('min_purchase', $coupon->min_purchase ?? 0) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Descuento máximo (S/) <span class="text-xs text-gray-400">(solo para %)</span></label>
        <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount', $coupon->max_discount ?? '') }}" class="w-full border rounded-lg px-3 py-2 mt-1" placeholder="50.00">
    </div>
    <div>
        <label class="text-sm font-medium">Límite de usos totales <span class="text-xs text-gray-400">(vacío = ilimitado)</span></label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" class="w-full border rounded-lg px-3 py-2 mt-1" min="1">
    </div>
    <div>
        <label class="text-sm font-medium">Límite por usuario <span class="text-xs text-gray-400">(vacío = ilimitado)</span></label>
        <input type="number" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user ?? 1) }}" class="w-full border rounded-lg px-3 py-2 mt-1" min="1">
    </div>
    <div>
        <label class="text-sm font-medium">Fecha de inicio <span class="text-xs text-gray-400">(opcional)</span></label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Fecha de expiración <span class="text-xs text-gray-400">(opcional)</span></label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>
    <div class="col-span-2 flex gap-6">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="active" value="1" {{ old('active', $coupon->active ?? true) ? 'checked' : '' }}> Cupón activo
        </label>
    </div>
</div>
