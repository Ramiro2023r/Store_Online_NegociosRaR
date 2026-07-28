@extends('layouts.admin')
@section('title', 'Productos - Admin')
@section('page-title', '📦 Gestión de Productos')

@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar producto..." class="border rounded-lg px-3 py-2 text-sm w-64">
        <button class="bg-gray-200 px-4 py-2 rounded-lg text-sm">Buscar</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="bg-rar-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-rar-700">+ Nuevo producto</a>
</div>

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Producto</th>
                <th class="px-4 py-3">Categoría</th>
                <th class="px-4 py-3">Precio</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($products as $product)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                    <td class="px-4 py-3">{{ $product->category->name }}</td>
                    <td class="px-4 py-3">S/ {{ number_format($product->price,2) }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $product->stock <= 5 ? 'text-red-600 font-semibold' : '' }}">{{ $product->stock }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $product->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $product->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:underline">Editar</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
