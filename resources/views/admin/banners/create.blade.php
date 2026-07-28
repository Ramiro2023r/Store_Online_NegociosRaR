@extends('layouts.admin')
@section('title', 'Nuevo Banner - Admin')
@section('page-title', '🖼️ Nuevo Banner')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded-xl p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Título *</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: Encuentra todo lo que necesitas">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Subtítulo</label>
            <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: Productos de calidad al mejor precio">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Texto del botón</label>
                <input type="text" name="button_text" value="{{ old('button_text') }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: Ver productos">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">URL del botón</label>
                <input type="text" name="button_url" value="{{ old('button_url') }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: /productos">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Imagen de fondo (opcional, 1920x600px recomendado)</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm">
            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Gradiente desde</label>
                <input type="text" name="gradient_from" value="{{ old('gradient_from', 'from-rar-700') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Gradiente hasta</label>
                <input type="text" name="gradient_to" value="{{ old('gradient_to', 'to-rar-500') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Color de texto</label>
                <input type="text" name="text_color" value="{{ old('text_color', 'text-white') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Orden</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }} class="rounded">
            <label for="active" class="text-sm">Activo</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-rar-600 text-white font-semibold px-6 py-2 rounded-lg text-sm hover:bg-rar-700">Guardar</button>
            <a href="{{ route('admin.banners.index') }}" class="text-gray-500 text-sm hover:underline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
