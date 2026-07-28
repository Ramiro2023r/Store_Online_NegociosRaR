@extends('layouts.admin')
@section('title', 'Conversación - Admin')
@section('page-title', '💬 Chat con ' . $conversation->user->name)

@section('content')
<div class="bg-white border rounded-xl flex flex-col h-[500px] max-w-3xl">
    <div class="flex-1 overflow-y-auto p-4 space-y-3">
        @foreach($conversation->messages as $msg)
            <div class="flex {{ $msg->is_staff ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[75%] {{ $msg->is_staff ? 'bg-rar-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-xl px-4 py-2 text-sm">
                    <div class="text-xs opacity-70 mb-1">{{ $msg->is_staff ? 'Soporte' : $conversation->user->name }}</div>
                    {{ $msg->body }}
                </div>
            </div>
        @endforeach
    </div>
    <form action="{{ route('admin.messages.reply', $conversation) }}" method="POST" class="border-t p-3 flex gap-2">
        @csrf
        <input type="text" name="body" placeholder="Escribe tu respuesta..." required class="flex-1 border rounded-full px-4 py-2 text-sm">
        <button class="bg-rar-600 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-rar-700">Enviar</button>
    </form>
</div>
@endsection
