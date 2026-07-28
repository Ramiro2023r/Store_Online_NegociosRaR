@extends('layouts.admin')
@section('title', 'Pedidos - Admin')
@section('page-title', '🧾 Ventas y Pedidos')

@section('content')
<form method="GET" class="mb-4 flex gap-2">
    <select name="status" onchange="this.form.submit()" class="border rounded-lg px-3 py-2 text-sm">
        <option value="">Todos los estados</option>
        @foreach(['pendiente','pagado','enviado','entregado','cancelado'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
</form>

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="px-4 py-3">Pedido</th><th class="px-4 py-3">Cliente</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Fecha</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y">
            @foreach($orders as $order)
                <tr>
                    <td class="px-4 py-3 font-medium">#{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">S/ {{ number_format($order->total,2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $order->status == 'entregado' ? 'bg-green-100 text-green-700' : ($order->status == 'cancelado' ? 'bg-red-100 text-red-700' : 'bg-cobre-100 text-cobre-700') }}">
                            {{ $order->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">Ver</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
