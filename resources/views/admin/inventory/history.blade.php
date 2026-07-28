@extends('layouts.admin')
@section('title', 'Historial de movimientos - Inventario')
@section('page-title', '📋 Historial de movimientos de stock')

@section('content')
<div class="bg-white border rounded-xl p-4 mb-6">
    <form method="GET" class="flex items-center gap-3 flex-wrap">
        @if(isset($product) && $product->exists)
            <input type="hidden" name="product" value="{{ $product->id }}">
            <span class="text-sm font-medium">Filtrando por: <a href="{{ route('admin.products.edit', $product) }}" class="text-rar-600 hover:underline">{{ $product->name }}</a></span>
            <a href="{{ route('admin.inventory.history') }}" class="text-xs text-gray-500 hover:underline">Limpiar filtro</a>
        @else
            <select name="product" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Todos los productos</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        @endif
        <select name="type" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">Todos los tipos</option>
            <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Venta</option>
            <option value="restock" {{ request('type') === 'restock' ? 'selected' : '' }}>Reabastecimiento</option>
            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Ajuste</option>
            <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Devolución</option>
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded-lg px-3 py-2 text-sm" placeholder="Desde">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded-lg px-3 py-2 text-sm" placeholder="Hasta">
        <button class="bg-rar-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rar-700">Filtrar</button>
        <a href="{{ route('admin.inventory.index') }}" class="text-sm text-rar-600 hover:underline">← Volver a inventario</a>
    </form>
</div>

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Producto</th>
                <th class="px-4 py-3">Variante</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Cantidad</th>
                <th class="px-4 py-3">Stock anterior</th>
                <th class="px-4 py-3">Nuevo stock</th>
                <th class="px-4 py-3">Responsable</th>
                <th class="px-4 py-3">Notas</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($movements as $m)
                <tr>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.products.edit', $m->product) }}" class="text-rar-600 hover:underline">{{ $m->product->name }}</a>
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($m->variant)
                            {{ $m->variant->size ?? '—' }} / {{ $m->variant->color ?? '—' }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded {{ $m->type === 'sale' ? 'bg-red-100 text-red-700' : ($m->type === 'restock' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($m->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold {{ $m->quantity < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                    </td>
                    <td class="px-4 py-3">{{ $m->previous_stock }}</td>
                    <td class="px-4 py-3">{{ $m->new_stock }}</td>
                    <td class="px-4 py-3 text-xs">{{ $m->user?->name ?? 'Sistema' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 max-w-[200px] truncate">{{ $m->notes ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Sin movimientos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $movements->links() }}</div>
@endsection
