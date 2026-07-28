@extends('layouts.admin')
@section('title', 'Editar Banner - Admin')
@section('page-title', '🖼️ Editar Banner')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded-xl p-6 space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Título *</label>
            <input type="text" name="title" value="{{ old('title', $banner->title) }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Subtítulo</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Texto del botón</label>
                <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">URL del botón</label>
                <input type="text" name="button_url" value="{{ old('button_url', $banner->button_url) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Imagen de fondo</label>
            @if($banner->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$banner->image) }}" class="h-24 rounded-lg object-cover">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-gray-400 mt-1">Dejar vacío para mantener la imagen actual.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Gradiente desde</label>
                <input type="text" name="gradient_from" value="{{ old('gradient_from', $banner->gradient_from) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Gradiente hasta</label>
                <input type="text" name="gradient_to" value="{{ old('gradient_to', $banner->gradient_to) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Color de texto</label>
                <input type="text" name="text_color" value="{{ old('text_color', $banner->text_color) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Orden</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1" {{ old('active', $banner->active) ? 'checked' : '' }} class="rounded">
            <label for="active" class="text-sm">Activo</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-rar-600 text-white font-semibold px-6 py-2 rounded-lg text-sm hover:bg-rar-700">Actualizar</button>
            <a href="{{ route('admin.banners.index') }}" class="text-gray-500 text-sm hover:underline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
