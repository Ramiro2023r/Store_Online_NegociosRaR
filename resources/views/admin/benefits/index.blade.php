@extends('layouts.admin')
@section('title', 'Beneficios - Admin')
@section('page-title', '✅ Beneficios del Home')

@section('content')
<div class="bg-white border rounded-xl overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Ícono</th>
                <th class="px-4 py-3">Título</th>
                <th class="px-4 py-3">Orden</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($benefits as $benefit)
                <tr>
                    <td class="px-4 py-3 text-2xl">{{ $benefit->icon }}</td>
                    <td class="px-4 py-3 font-medium">{{ $benefit->title }}</td>
                    <td class="px-4 py-3">{{ $benefit->sort_order }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $benefit->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $benefit->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-3">
                        <button onclick="editBenefit({{ $benefit->id }})" class="text-blue-600 hover:underline">Editar</button>
                        <form action="{{ route('admin.benefits.destroy', $benefit) }}" method="POST" onsubmit="return confirm('¿Eliminar este beneficio?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Crear nuevo --}}
<div class="bg-white border rounded-xl p-6 max-w-lg">
    <h3 class="font-semibold mb-3">+ Agregar beneficio</h3>
    <form action="{{ route('admin.benefits.store') }}" method="POST" class="space-y-3">
        @csrf
        <div class="grid grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium mb-1">Ícono (emoji)</label>
                <input type="text" name="icon" required class="w-full border rounded-lg px-3 py-2 text-sm text-center" placeholder="📦">
            </div>
            <div class="col-span-3">
                <label class="block text-xs font-medium mb-1">Título</label>
                <input type="text" name="title" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: Envío rápido">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Orden</label>
                <input type="number" name="sort_order" value="0" min="0" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="new-active" value="1" checked class="rounded">
            <label for="new-active" class="text-sm">Activo</label>
        </div>
        <button type="submit" class="bg-rar-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-rar-700">Guardar</button>
    </form>
</div>

{{-- Modal editar --}}
<div id="edit-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="font-semibold mb-3">Editar beneficio</h3>
        <form id="edit-form" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium mb-1">Ícono (emoji)</label>
                <input type="text" name="icon" id="edit-icon" required class="w-full border rounded-lg px-3 py-2 text-sm text-center">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Título</label>
                <input type="text" name="title" id="edit-title" required class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Orden</label>
                <input type="number" name="sort_order" id="edit-sort" min="0" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="active" id="edit-active" value="1" class="rounded">
                <label for="edit-active" class="text-sm">Activo</label>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-rar-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-rar-700">Actualizar</button>
                <button type="button" onclick="closeEditModal()" class="text-gray-500 text-sm hover:underline">Cancelar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const benefits = @json($benefits);
function editBenefit(id) {
    const b = benefits.find(b => b.id === id);
    if (!b) return;
    document.getElementById('edit-icon').value = b.icon;
    document.getElementById('edit-title').value = b.title;
    document.getElementById('edit-sort').value = b.sort_order;
    document.getElementById('edit-active').checked = b.active;
    document.getElementById('edit-form').action = '{{ route('admin.benefits.index') }}/' + id;
    document.getElementById('edit-modal').classList.remove('hidden');
    document.getElementById('edit-modal').classList.add('flex');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    document.getElementById('edit-modal').classList.remove('flex');
}
</script>
@endpush
@endsection
