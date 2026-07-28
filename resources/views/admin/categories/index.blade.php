@extends('layouts.admin')
@section('title', 'Categorías - Admin')
@section('page-title', '🗂️ Gestión de Categorías')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white border rounded-xl p-6 h-fit">
        <h3 class="font-bold mb-4">Nueva categoría</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="text-sm font-medium">Nombre</label>
                <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Ícono (emoji)</label>
                <input type="text" name="icon" placeholder="🛍️" class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Descripción</label>
                <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1"></textarea>
            </div>
            <button class="w-full bg-rar-600 text-white font-semibold py-2 rounded-lg text-sm hover:bg-rar-700">Crear categoría</button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white border rounded-xl overflow-hidden h-fit">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="px-4 py-3">Categoría</th><th class="px-4 py-3">Productos</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Acciones</th></tr>
            </thead>
            <tbody class="divide-y">
                @foreach($categories as $cat)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $cat->icon }} {{ $cat->name }}</td>
                        <td class="px-4 py-3">{{ $cat->products_count }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.categories.update', $cat) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="name" value="{{ $cat->name }}">
                                <input type="hidden" name="icon" value="{{ $cat->icon }}">
                                <label class="inline-flex items-center gap-1 text-xs">
                                    <input type="checkbox" name="active" value="1" onchange="this.form.submit()" {{ $cat->active ? 'checked' : '' }}> Activa
                                </label>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('¿Eliminar categoría?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
