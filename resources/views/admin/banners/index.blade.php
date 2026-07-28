@extends('layouts.admin')
@section('title', 'Banners - Admin')
@section('page-title', '🖼️ Banners del Carrusel')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div></div>
    <a href="{{ route('admin.banners.create') }}" class="bg-rar-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-rar-700">+ Nuevo banner</a>
</div>

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Imagen</th>
                <th class="px-4 py-3">Título</th>
                <th class="px-4 py-3">Subtítulo</th>
                <th class="px-4 py-3">Botón</th>
                <th class="px-4 py-3">Orden</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($banners as $banner)
                <tr>
                    <td class="px-4 py-3">{{ $banner->id }}</td>
                    <td class="px-4 py-3">
                        @if($banner->image)
                            <img src="{{ asset('storage/'.$banner->image) }}" class="h-12 w-20 object-cover rounded">
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $banner->title }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $banner->subtitle ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($banner->button_text)
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $banner->button_text }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $banner->sort_order }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $banner->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $banner->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-3">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="text-blue-600 hover:underline">Editar</a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('¿Eliminar este banner?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4 text-gray-400 text-xs">Los banners se muestran en el carrusel del inicio según su orden.</div>
@endsection
