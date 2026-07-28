@extends('layouts.admin')
@section('title', 'Reabastecer - ' . $product->name)
@section('page-title', '📦 Reabastecer: ' . $product->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.inventory.index') }}" class="text-sm text-rar-600 hover:underline">← Volver a inventario</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @if($product->activeVariants->count())
        @foreach($product->activeVariants as $v)
            <div class="bg-white border rounded-xl p-6">
                <h3 class="font-bold">{{ $v->size ?? '—' }} / {{ $v->color ?? '—' }}</h3>
                <p class="text-sm text-gray-500 mb-1">Stock actual: <strong>{{ $v->stock }}</strong></p>
                @if($v->sku)
                    <p class="text-xs text-gray-400 mb-3">SKU: {{ $v->sku }}</p>
                @endif
                <form action="{{ route('admin.inventory.restock.store', ['product' => $product, 'variant' => $v]) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="number" name="quantity" min="1" required class="w-24 border rounded-lg px-3 py-2 text-sm" placeholder="Cant.">
                    <input type="text" name="notes" class="flex-1 border rounded-lg px-3 py-2 text-sm" placeholder="Notas (opcional)">
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700">+ Agregar</button>
                </form>
            </div>
        @endforeach
    @endif

    {{-- Reabastecimiento directo al producto (sin variante) --}}
    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-bold">Stock general del producto</h3>
        <p class="text-sm text-gray-500 mb-3">Stock actual del producto: <strong>{{ $product->stock }}</strong></p>
        @if($product->activeVariants->count())
            <p class="text-xs text-gray-400 mb-3">💡 El producto tiene variantes activas. Se recomienda reabastecer cada variante por separado arriba. El stock total se actualizará automáticamente.</p>
        @endif
        <form action="{{ route('admin.inventory.restock.store', ['product' => $product]) }}" method="POST" class="flex gap-2">
            @csrf
            <input type="number" name="quantity" min="1" required class="w-24 border rounded-lg px-3 py-2 text-sm" placeholder="Cant.">
            <input type="text" name="notes" class="flex-1 border rounded-lg px-3 py-2 text-sm" placeholder="Notas (opcional)">
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700">+ Agregar</button>
        </form>
    </div>
</div>
@endsection
