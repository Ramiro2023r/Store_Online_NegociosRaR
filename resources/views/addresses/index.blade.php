@extends('layouts.app')
@section('title', 'Mis direcciones - Negocios RaR')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">📍 Mis direcciones</h1>
        <a href="{{ route('account.index') }}" class="text-sm text-rar-600 hover:underline">&larr; Volver a mi cuenta</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @foreach($addresses as $addr)
            <div class="bg-white border rounded-xl p-4 {{ $addr->is_default ? 'ring-2 ring-rar-500' : '' }}">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-block bg-gray-100 text-xs font-semibold px-2 py-0.5 rounded">{{ $addr->label }}</span>
                        @if($addr->is_default)
                            <span class="inline-block bg-rar-100 text-rar-700 text-xs font-semibold px-2 py-0.5 rounded ml-1">Principal</span>
                        @endif
                    </div>
                    <div class="flex gap-2 text-xs">
                        <button onclick="editAddress({{ $addr->id }})" class="text-blue-600 hover:underline">Editar</button>
                        <form action="{{ route('addresses.destroy', $addr) }}" method="POST" onsubmit="return confirm('¿Eliminar esta dirección?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </div>
                </div>
                <p class="text-sm mt-2">{{ $addr->address }}</p>
                @if($addr->city)<p class="text-xs text-gray-500">{{ $addr->city }}</p>@endif
                @if($addr->phone)<p class="text-xs text-gray-500">📞 {{ $addr->phone }}</p>@endif
            </div>
        @endforeach

        {{-- Agregar nueva --}}
        <div class="bg-white border border-dashed rounded-xl p-4 flex items-center justify-center min-h-[120px]">
            <button onclick="openNewAddress()" class="text-rar-600 text-sm font-semibold hover:underline">+ Agregar dirección</button>
        </div>
    </div>

    {{-- Modal nueva/editar dirección --}}
    <div id="address-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="font-semibold mb-3" id="modal-title">Nueva dirección</h3>
            <form id="address-form" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div>
                    <label class="block text-xs font-medium mb-1">Etiqueta</label>
                    <select name="label" id="field-label" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="Casa">Casa</option>
                        <option value="Trabajo">Trabajo</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Dirección *</label>
                    <input type="text" name="address" id="field-address" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Ciudad</label>
                        <input type="text" name="city" id="field-city" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Teléfono *</label>
                        <input type="text" name="phone" id="field-phone" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" id="field-default" value="1" class="rounded">
                    <label for="field-default" class="text-sm">Establecer como dirección principal</label>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-rar-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-rar-700">Guardar</button>
                    <button type="button" onclick="closeAddressModal()" class="text-gray-500 text-sm hover:underline">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const addresses = @json($addresses);

function openNewAddress() {
    document.getElementById('modal-title').textContent = 'Nueva dirección';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('address-form').action = '{{ route('addresses.store') }}';
    document.getElementById('field-label').value = 'Casa';
    document.getElementById('field-address').value = '';
    document.getElementById('field-city').value = '';
    document.getElementById('field-phone').value = '';
    document.getElementById('field-default').checked = false;
    document.getElementById('address-modal').classList.remove('hidden');
    document.getElementById('address-modal').classList.add('flex');
}

function editAddress(id) {
    const a = addresses.find(a => a.id === id);
    if (!a) return;
    document.getElementById('modal-title').textContent = 'Editar dirección';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('address-form').action = '{{ route('addresses.index') }}/' + id;
    document.getElementById('field-label').value = a.label;
    document.getElementById('field-address').value = a.address;
    document.getElementById('field-city').value = a.city || '';
    document.getElementById('field-phone').value = a.phone || '';
    document.getElementById('field-default').checked = a.is_default;
    document.getElementById('address-modal').classList.remove('hidden');
    document.getElementById('address-modal').classList.add('flex');
}

function closeAddressModal() {
    document.getElementById('address-modal').classList.add('hidden');
    document.getElementById('address-modal').classList.remove('flex');
}
</script>
@endpush
@endsection
