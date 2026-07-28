@extends('layouts.admin')
@section('title', 'Reseñas - Panel Administrativo')
@section('page-title', '⭐ Gestión de Reseñas')

@section('content')
@if($pendingCount > 0)
    <div class="bg-cobre-50 border border-cobre-200 text-cobre-800 text-sm rounded-lg px-4 py-3 mb-6">
        ⚠️ Tienes <strong>{{ $pendingCount }}</strong> reseña(s) pendiente(s) de aprobación.
    </div>
@endif

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Producto</th>
                <th class="px-4 py-3">Usuario</th>
                <th class="px-4 py-3">Calificación</th>
                <th class="px-4 py-3">Comentario</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($reviews as $review)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('products.show', $review->product) }}" class="text-rar-600 hover:underline" target="_blank">{{ $review->product->name }}</a>
                    </td>
                    <td class="px-4 py-3">{{ $review->user->name }}</td>
                    <td class="px-4 py-3">
                        <div class="flex text-cobre-500">
                            @for($i=1;$i<=5;$i++)
                                <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                    </td>
                    <td class="px-4 py-3 max-w-xs truncate">{{ $review->comment ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($review->approved)
                            <span class="text-green-600 font-medium text-xs bg-green-50 px-2 py-1 rounded">Aprobada</span>
                        @else
                            <span class="text-cobre-600 font-medium text-xs bg-cobre-50 px-2 py-1 rounded">Pendiente</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $review->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 flex gap-1">
                        @if(!$review->approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded hover:bg-green-100 font-medium">Aprobar</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('¿Eliminar esta reseña?')">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-50 text-red-600 px-2 py-1 rounded hover:bg-red-100 font-medium">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay reseñas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $reviews->links() }}
</div>
@endsection
