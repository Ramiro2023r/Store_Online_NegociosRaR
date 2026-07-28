@extends('layouts.admin')
@section('title', 'Inventario - Admin')
@section('page-title', '📦 Gestión de inventario')

@section('content')
{{-- Resumen --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Total productos</div>
        <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Con stock</div>
        <div class="text-2xl font-bold text-green-600">{{ $stats['in_stock'] }}</div>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Stock bajo</div>
        <div class="text-2xl font-bold text-cobre-600">
            {{ $stats['low_stock'] }}
            @if($stats['low_stock'] > 0)
                <a href="{{ route('admin.inventory.index', ['alert' => 'low']) }}" class="text-xs underline">ver</a>
            @endif
        </div>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Agotados</div>
        <div class="text-2xl font-bold text-red-600">
            {{ $stats['out_of_stock'] }}
            @if($stats['out_of_stock'] > 0)
                <a href="{{ route('admin.inventory.index', ['alert' => 'out']) }}" class="text-xs underline">ver</a>
            @endif
        </div>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Requieren reabastecimiento</div>
        <div class="text-2xl font-bold text-cobre-600">
            {{ $stats['needs_restock'] }}
        </div>
    </div>
</div>

{{-- Filtro / búsqueda --}}
<div class="bg-white border rounded-xl p-4 mb-6">
    <form method="GET" class="flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o SKU..." class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <select name="alert" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">Todos</option>
            <option value="low" {{ request('alert') === 'low' ? 'selected' : '' }}>Stock bajo</option>
            <option value="out" {{ request('alert') === 'out' ? 'selected' : '' }}>Agotados</option>
        </select>
        <button class="bg-rar-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rar-700">Filtrar</button>
        <a href="{{ route('admin.inventory.history') }}" class="text-sm text-rar-600 hover:underline">📋 Historial de movimientos</a>
    </form>
</div>

{{-- Tabla --}}
<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Producto</th>
                <th class="px-4 py-3">SKU</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Mínimo</th>
                <th class="px-4 py-3">Variantes</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($products as $p)
                <tr>
                    <td class="px-4 py-3 font-medium">
                        @if($p->active)
                            {{ $p->name }}
                        @else
                            <span class="text-gray-400">{{ $p->name }} <span class="text-xs">(inactivo)</span></span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $p->sku ?? '—' }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $p->stock }}</td>
                    <td class="px-4 py-3">{{ $p->min_stock }}</td>
                    <td class="px-4 py-3">
                        @if($p->active_variants_count > 0)
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $p->active_variants_count }} vars.</span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($p->stock <= 0)
                            <span class="text-red-600 font-medium">Agotado</span>
                        @elseif($p->stock <= $p->min_stock)
                            <span class="text-cobre-600 font-medium">Stock bajo</span>
                        @else
                            <span class="text-green-600 font-medium">OK</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.inventory.restock', $p) }}" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">+ Reabastecer</a>
                        <a href="{{ route('admin.inventory.history', ['product' => $p->id]) }}" class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200">Historial</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Sin productos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
