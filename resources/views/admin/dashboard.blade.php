@extends('layouts.admin')
@section('title', 'Dashboard - Negocios RaR')
@section('page-title', '📊 Dashboard General')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-8">
    <div class="bg-white border rounded-xl p-5">
        <div class="text-xs text-gray-400 mb-1">Ventas totales</div>
        <div class="text-2xl font-bold text-rar-600">S/ {{ number_format($totalVentas,2) }}</div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <div class="text-xs text-gray-400 mb-1">Pedidos de hoy</div>
        <div class="text-2xl font-bold">{{ $pedidosHoy }}</div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <div class="text-xs text-gray-400 mb-1">Total productos</div>
        <div class="text-2xl font-bold">{{ $totalProductos }}</div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <div class="text-xs text-gray-400 mb-1">Clientes registrados</div>
        <div class="text-2xl font-bold">{{ $totalClientes }}</div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <div class="text-xs text-gray-400 mb-1">Cupones activos</div>
        <div class="text-2xl font-bold">{{ $cuponesActivos }}</div>
    </div>
</div>

@php
    $needsRestockCount = \App\Models\Product::where('stock', '>', 0)->whereRaw('stock <= min_stock')->count();
    $outOfStockCount = \App\Models\Product::where('stock', '<=', 0)->where('active', true)->count();
@endphp
@if($needsRestockCount > 0)
    <div class="bg-cobre-50 border border-cobre-200 text-cobre-800 text-sm rounded-lg px-4 py-3 mb-6">
        📦 Tienes <strong>{{ $needsRestockCount }}</strong> producto(s) que requieren reabastecimiento.
        <a href="{{ route('admin.inventory.index', ['alert' => 'low']) }}" class="underline font-semibold">Ver inventario</a>
    </div>
@endif
@if($outOfStockCount > 0)
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg px-4 py-3 mb-6">
        ⚠️ Hay <strong>{{ $outOfStockCount }}</strong> producto(s) agotados.
        <a href="{{ route('admin.inventory.index', ['alert' => 'out']) }}" class="underline font-semibold">Ver agotados</a>
    </div>
@endif

@if($reviewsPendientes > 0)
    <div class="bg-cobre-50 border border-cobre-200 text-cobre-800 text-sm rounded-lg px-4 py-3 mb-6">
        ⭐ Tienes <strong>{{ $reviewsPendientes }}</strong> reseña(s) pendiente(s) de aprobación.
        <a href="{{ route('admin.reviews.index') }}" class="underline font-semibold">Revisar reseñas</a>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-bold mb-4">Últimos pedidos</h3>
        <div class="space-y-2">
            @forelse($ultimosPedidos as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between text-sm border-b pb-2 hover:text-rar-600">
                    <span>#{{ $order->order_number }} - {{ $order->user->name ?? 'N/A' }}</span>
                    <span class="font-semibold">S/ {{ number_format($order->total,2) }}</span>
                </a>
            @empty
                <p class="text-sm text-gray-400">Aún no hay pedidos.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-bold mb-4">Productos más vendidos</h3>
        <div class="space-y-2">
            @forelse($productosMasVendidos as $p)
                <div class="flex items-center justify-between text-sm border-b pb-2">
                    <span>{{ $p->product_name }}</span>
                    <span class="font-semibold text-rar-600">{{ $p->vendidos }} vendidos</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Sin datos de ventas todavía.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-bold mb-4">❤️ Productos más deseados</h3>
        <div class="space-y-2">
            @forelse($productosMasDeseados as $item)
                <div class="flex items-center justify-between text-sm border-b pb-2">
                    <span>{{ $item->product->name ?? '—' }}</span>
                    <span class="font-semibold text-cobre-500">{{ $item->veces }} en lista(s)</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Aún no hay productos en listas de deseos.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
