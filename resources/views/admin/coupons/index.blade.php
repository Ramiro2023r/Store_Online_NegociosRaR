@extends('layouts.admin')
@section('title', 'Cupones - Admin')
@section('page-title', '🏷️ Gestión de Cupones')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div></div>
    <a href="{{ route('admin.coupons.create') }}" class="bg-rar-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-rar-700">+ Nuevo cupón</a>
</div>

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Código</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Valor</th>
                <th class="px-4 py-3">Categoría</th>
                <th class="px-4 py-3">Usos</th>
                <th class="px-4 py-3">Expira</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($coupons as $coupon)
                <tr>
                    <td class="px-4 py-3 font-mono font-semibold">{{ $coupon->code }}</td>
                    <td class="px-4 py-3">{{ $coupon->type === 'percentage' ? '%' : 'S/ fijo' }}</td>
                    <td class="px-4 py-3">
                        {{ $coupon->type === 'percentage' ? $coupon->value.'%' : 'S/ '.number_format($coupon->value,2) }}
                        @if($coupon->max_discount && $coupon->type === 'percentage')
                            <span class="text-xs text-gray-400">(tope S/ {{ number_format($coupon->max_discount,2) }})</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $coupon->category->name ?? 'Todas' }}</td>
                    <td class="px-4 py-3">
                        {{ $coupon->usage_count }}
                        @if($coupon->usage_limit)
                            / {{ $coupon->usage_limit }}
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($coupon->expires_at)
                            @if($coupon->expires_at->isPast())
                                <span class="text-red-600 font-medium">Vencido</span>
                            @else
                                <span class="text-gray-600">{{ $coupon->expires_at->format('d/m/Y') }}</span>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $coupon->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $coupon->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-3">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-blue-600 hover:underline">Editar</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('¿Eliminar este cupón?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
@endsection
