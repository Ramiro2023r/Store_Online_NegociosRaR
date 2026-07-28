@extends('layouts.admin')
@section('title', 'Newsletter - Admin')
@section('page-title', '📧 Suscriptores Newsletter')

@section('content')
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500">{{ $subscribers->total() }} suscriptor(es) registrado(s)</p>
    <a href="{{ route('admin.newsletters.export') }}" class="bg-rar-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rar-700">📥 Exportar emails</a>
</div>

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="px-4 py-3">Email</th><th class="px-4 py-3">Nombre</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Fecha</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y">
            @forelse($subscribers as $sub)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $sub->email }}</td>
                    <td class="px-4 py-3">{{ $sub->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $sub->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $sub->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $sub->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.newsletters.destroy', $sub) }}" method="POST" onsubmit="return confirm('¿Eliminar este suscriptor?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay suscriptores todavía.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $subscribers->links() }}</div>
@endsection
