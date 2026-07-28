@extends('layouts.admin')
@section('title', 'Variantes - ' . $product->name)
@section('page-title', '📦 Variantes de: ' . $product->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.products.edit', $product) }}" class="text-sm text-rar-600 hover:underline">← Volver al producto</a>
</div>

{{-- Nueva variante --}}
<div class="bg-white border rounded-xl p-6 mb-6" x-data="{ open: false }">
    <button @click="open = !open" class="bg-rar-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rar-700" x-text="open ? '✕ Cancelar' : '+ Agregar variante'"></button>

    <form x-show="open" action="{{ route('admin.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4" x-cloak>
        @csrf
        <div>
            <label class="text-xs font-medium">Talla</label>
            <input type="text" name="size" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: M, 42, Único">
        </div>
        <div>
            <label class="text-xs font-medium">Color</label>
            <input type="text" name="color" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: Rojo, Negro">
        </div>
        <div>
            <label class="text-xs font-medium">SKU (opcional)</label>
            <input type="text" name="sku" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Único por variante">
        </div>
        <div>
            <label class="text-xs font-medium">Stock</label>
            <input type="number" name="stock" value="0" min="0" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-medium">Precio (opcional)</label>
            <input type="number" step="0.01" name="price" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Usa el del producto">
        </div>
        <div>
            <label class="text-xs font-medium">Precio tachado</label>
            <input type="number" step="0.01" name="compare_price" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-medium">Imagen</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <button class="bg-rar-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rar-700">Guardar</button>
        </div>
    </form>
</div>

{{-- Listado --}}
<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Talla</th>
                <th class="px-4 py-3">Color</th>
                <th class="px-4 py-3">SKU</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Precio</th>
                <th class="px-4 py-3">Activo</th>
                <th class="px-4 py-3">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($product->variants as $v)
                <tr>
                    <td class="px-4 py-3">{{ $v->size ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($v->color)
                            <span class="inline-block w-4 h-4 rounded-full align-middle mr-1" style="background: {{ $v->color }}"></span>
                            {{ $v->color }}
                        @else — @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $v->sku ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $v->stock <= 0 ? 'text-red-600 font-medium' : ($v->isLowStock() ? 'text-cobre-600 font-medium' : 'text-green-600') }}">
                            {{ $v->stock }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($v->price)
                            @if($v->compare_price)
                                <span class="line-through text-gray-400 text-xs">S/{{ number_format($v->compare_price,2) }}</span>
                            @endif
                            S/ {{ number_format($v->price,2) }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $v->active ? '✓' : '✗' }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.inventory.restock', ['product' => $product, 'variant' => $v]) }}" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">Reabastecer</a>
                        <a href="{{ route('admin.products.variants.edit', [$product, $v]) }}" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">Editar</a>
                        <form action="{{ route('admin.products.variants.destroy', [$product, $v]) }}" method="POST" onsubmit="return confirm('¿Eliminar esta variante?')">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Sin variantes aún. Agrega combinaciones de talla y color.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
