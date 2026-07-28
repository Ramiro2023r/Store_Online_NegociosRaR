@extends('layouts.app')
@section('title', 'Contáctanos - Negocios RaR')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-2">💬 Contáctanos</h1>
    <p class="text-gray-500 mb-6 text-sm">Chatea en tiempo real con nuestro equipo de soporte. Te responderemos a la brevedad.</p>

    <div class="bg-white border rounded-xl flex flex-col h-[500px]">
        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="chat-box">
            @forelse($conversation->messages as $msg)
                <div class="flex {{ $msg->is_staff ? 'justify-start' : 'justify-end' }}">
                    <div class="max-w-[75%] {{ $msg->is_staff ? 'bg-gray-100 text-gray-800' : 'bg-rar-600 text-white' }} rounded-xl px-4 py-2 text-sm">
                        <div class="text-xs opacity-70 mb-1">{{ $msg->is_staff ? 'Soporte Negocios RaR' : 'Tú' }}</div>
                        {{ $msg->body }}
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 text-sm py-10">
                    Envía tu primer mensaje y nuestro equipo te responderá pronto. 👋
                </div>
            @endforelse
        </div>
        <form action="{{ route('contact.send') }}" method="POST" class="border-t p-3 flex gap-2">
            @csrf
            <input type="text" name="body" placeholder="Escribe tu mensaje..." required class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rar-500">
            <button class="bg-rar-600 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-rar-700">Enviar</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 text-sm text-center">
        <div class="bg-white border rounded-xl p-4">📞 (01) 555-0100</div>
        <div class="bg-white border rounded-xl p-4">✉️ ventas@negociosrar.com</div>
        <div class="bg-white border rounded-xl p-4">🕐 Lun-Sáb 9am - 8pm</div>
    </div>
</div>
<script>
    const box = document.getElementById('chat-box');
    box.scrollTop = box.scrollHeight;
</script>
@endsection
