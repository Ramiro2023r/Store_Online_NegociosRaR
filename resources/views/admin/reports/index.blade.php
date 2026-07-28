@extends('layouts.admin')
@section('title', 'Reportes - Admin')
@section('page-title', '📊 Reportes de ventas')

@section('content')
<div class="bg-white border rounded-xl p-4 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="text-xs font-medium text-gray-500">Desde</label>
            <input type="date" name="from" value="{{ $start->format('Y-m-d') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-medium text-gray-500">Hasta</label>
            <input type="date" name="to" value="{{ $end->format('Y-m-d') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button class="bg-rar-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rar-700">Filtrar</button>
        <span class="text-sm text-gray-400 ml-auto">Período: {{ $start->format('d/m/Y') }} — {{ $end->format('d/m/Y') }}</span>
    </form>
</div>

{{-- Resumen --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Pedidos</div>
        <div class="text-2xl font-bold">{{ $resumen['pedidos'] }}</div>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Subtotal</div>
        <div class="text-2xl font-bold text-rar-600">S/ {{ number_format($resumen['subtotal'], 2) }}</div>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Envío</div>
        <div class="text-2xl font-bold text-cobre-600">S/ {{ number_format($resumen['envio'], 2) }}</div>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <div class="text-xs text-gray-400">Total</div>
        <div class="text-2xl font-bold text-rar-600">S/ {{ number_format($resumen['total'], 2) }}</div>
    </div>
</div>

{{-- Ventas por período --}}
<div class="bg-white border rounded-xl mb-6">
    <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-bold">📅 Ventas por día</h3>
        <a href="{{ route('admin.reports.export', ['type' => 'period', 'from' => $start->format('Y-m-d'), 'to' => $end->format('Y-m-d')]) }}" class="text-sm text-rar-600 hover:underline">📥 Exportar CSV</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="px-4 py-3">Fecha</th><th class="px-4 py-3">Pedidos</th><th class="px-4 py-3">Subtotal</th><th class="px-4 py-3">Envío</th><th class="px-4 py-3">Total</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($periodSales as $row)
                    <tr>
                        <td class="px-4 py-3">{{ Carbon\Carbon::parse($row->fecha)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $row->pedidos }}</td>
                        <td class="px-4 py-3">S/ {{ number_format($row->subtotal, 2) }}</td>
                        <td class="px-4 py-3">S/ {{ number_format($row->envio, 2) }}</td>
                        <td class="px-4 py-3 font-semibold">S/ {{ number_format($row->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin ventas en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Ventas por categoría --}}
<div class="bg-white border rounded-xl mb-6">
    <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-bold">🏷️ Ventas por categoría</h3>
        <a href="{{ route('admin.reports.export', ['type' => 'category', 'from' => $start->format('Y-m-d'), 'to' => $end->format('Y-m-d')]) }}" class="text-sm text-rar-600 hover:underline">📥 Exportar CSV</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="px-4 py-3">Categoría</th><th class="px-4 py-3">Pedidos</th><th class="px-4 py-3">Unidades vendidas</th><th class="px-4 py-3">Total</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($byCategory as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $row->category }}</td>
                        <td class="px-4 py-3">{{ $row->pedidos }}</td>
                        <td class="px-4 py-3">{{ $row->vendidos }}</td>
                        <td class="px-4 py-3 font-semibold">S/ {{ number_format($row->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Sin ventas en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Ventas por producto --}}
<div class="bg-white border rounded-xl">
    <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-bold">🛍️ Ventas por producto (top 100)</h3>
        <a href="{{ route('admin.reports.export', ['type' => 'product', 'from' => $start->format('Y-m-d'), 'to' => $end->format('Y-m-d')]) }}" class="text-sm text-rar-600 hover:underline">📥 Exportar CSV</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="px-4 py-3">Producto</th><th class="px-4 py-3">Unidades vendidas</th><th class="px-4 py-3">Total</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($byProduct as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $row->product_name }}</td>
                        <td class="px-4 py-3">{{ $row->vendidos }}</td>
                        <td class="px-4 py-3 font-semibold">S/ {{ number_format($row->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">Sin ventas en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 text-xs text-gray-400">
    💡 Los reportes consideran pedidos con estado: Pagado, Enviado o Entregado.
    Usa el filtro de fechas para cambiar el período. Haz clic en "Exportar CSV" para descargar.
</div>
@endsection
