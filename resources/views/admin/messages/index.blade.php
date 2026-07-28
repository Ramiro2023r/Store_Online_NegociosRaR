@extends('layouts.admin')
@section('title', 'Mensajes - Admin')
@section('page-title', '💬 Mensajes de Clientes')

@section('content')
<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="px-4 py-3">Cliente</th><th class="px-4 py-3">Asunto</th><th class="px-4 py-3">Mensajes</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y">
            @forelse($conversations as $conv)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $conv->user->name }}</td>
                    <td class="px-4 py-3">{{ $conv->subject }}</td>
                    <td class="px-4 py-3">{{ $conv->messages->count() }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $conv->status == 'abierta' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($conv->status) }}</span>
                    </td>
                    <td class="px-4 py-3"><a href="{{ route('admin.messages.show', $conv) }}" class="text-blue-600 hover:underline">Responder</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay conversaciones todavía.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
