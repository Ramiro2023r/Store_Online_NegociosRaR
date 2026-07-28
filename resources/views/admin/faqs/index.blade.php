@extends('layouts.admin')
@section('title', 'FAQ - Admin')
@section('page-title', '❓ Preguntas Frecuentes')

@section('content')
<div class="bg-white border rounded-xl overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Categoría</th>
                <th class="px-4 py-3">Pregunta</th>
                <th class="px-4 py-3">Orden</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($faqs as $faq)
                <tr>
                    <td class="px-4 py-3 text-xs"><span class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $faq->category }}</span></td>
                    <td class="px-4 py-3 font-medium">{{ $faq->question }}</td>
                    <td class="px-4 py-3">{{ $faq->sort_order }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $faq->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $faq->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-3">
                        <button onclick="editFaq({{ $faq->id }})" class="text-blue-600 hover:underline">Editar</button>
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('¿Eliminar esta FAQ?')">
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
<div class="bg-white border rounded-xl p-6 max-w-2xl">
    <h3 class="font-semibold mb-3">+ Agregar FAQ</h3>
    <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-3">
        @csrf
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium mb-1">Categoría</label>
                <select name="category" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="general">General</option>
                    <option value="envio">Envío</option>
                    <option value="pago">Pago</option>
                    <option value="devolucion">Devolución</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Orden</label>
                <input type="number" name="sort_order" value="0" min="0" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex items-end gap-2">
                <input type="checkbox" name="active" id="new-active" value="1" checked class="rounded">
                <label for="new-active" class="text-sm">Activo</label>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Pregunta</label>
            <input type="text" name="question" required class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Respuesta</label>
            <textarea name="answer" required rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
        </div>
        <button type="submit" class="bg-rar-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-rar-700">Guardar</button>
    </form>
</div>

{{-- Modal editar --}}
<div id="edit-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg">
        <h3 class="font-semibold mb-3">Editar FAQ</h3>
        <form id="edit-form" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1">Categoría</label>
                    <select name="category" id="edit-category" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="general">General</option>
                        <option value="envio">Envío</option>
                        <option value="pago">Pago</option>
                        <option value="devolucion">Devolución</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Orden</label>
                    <input type="number" name="sort_order" id="edit-sort" min="0" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Pregunta</label>
                <input type="text" name="question" id="edit-question" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Respuesta</label>
                <textarea name="answer" id="edit-answer" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
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
const faqs = @json($faqs);
function editFaq(id) {
    const f = faqs.find(f => f.id === id);
    if (!f) return;
    document.getElementById('edit-question').value = f.question;
    document.getElementById('edit-answer').value = f.answer;
    document.getElementById('edit-category').value = f.category;
    document.getElementById('edit-sort').value = f.sort_order;
    document.getElementById('edit-active').checked = f.active;
    document.getElementById('edit-form').action = '{{ route('admin.faqs.index') }}/' + id;
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
