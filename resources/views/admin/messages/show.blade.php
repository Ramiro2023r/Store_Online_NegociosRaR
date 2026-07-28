@extends('layouts.admin')
@section('title', 'Conversación - Admin')
@section('page-title', '💬 Chat con ' . $conversation->user->name)

@section('content')
<div class="bg-white border rounded-xl flex flex-col h-[500px] max-w-3xl"
    x-data="adminChat(@json($conversation->messages->map(fn($m) => [
        'id' => $m->id,
        'body' => $m->body,
        'is_staff' => $m->is_staff,
        'user_name' => $m->is_staff ? 'Soporte' : $conversation->user->name,
    ])))" x-init="initAdminChat()">
    <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="chatbox">
        <template x-for="msg in messages" :key="msg.id">
            <div :class="'flex ' + (msg.is_staff ? 'justify-end' : 'justify-start')">
                <div :class="'max-w-[75%] rounded-xl px-4 py-2 text-sm ' + (msg.is_staff ? 'bg-rar-600 text-white' : 'bg-gray-100 text-gray-800')">
                    <div class="text-xs opacity-70 mb-1" x-text="msg.user_name"></div>
                    <div x-text="msg.body"></div>
                </div>
            </div>
        </template>
    </div>
    <form class="border-t p-3 flex gap-2" @submit.prevent="sendAdminReply">
        @csrf
        <input type="text" x-model="newMsg" placeholder="Escribe tu respuesta..." required
            class="flex-1 border rounded-full px-4 py-2 text-sm">
        <button class="bg-rar-600 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-rar-700" :disabled="sending">Enviar</button>
    </form>
</div>
<script>
function adminChat(initial) {
    return {
        messages: initial || [],
        newMsg: '',
        sending: false,
        initAdminChat() {
            this.scrollDown();
            setInterval(() => this.pollAdmin(), 3000);
        },
        scrollDown() {
            this.$nextTick(() => {
                const box = this.$refs.chatbox;
                if (box) box.scrollTop = box.scrollHeight;
            });
        },
        async pollAdmin() {
            const lastId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;
            try {
                const res = await fetch('{{ route("admin.messages.messages", $conversation) }}?after_id=' + lastId);
                const data = await res.json();
                if (data.length) {
                    data.forEach(m => this.messages.push(m));
                    this.scrollDown();
                }
            } catch (e) {}
        },
        async sendAdminReply() {
            if (!this.newMsg.trim()) return;
            this.sending = true;
            try {
                const res = await fetch('{{ route("admin.messages.reply", $conversation) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ body: this.newMsg }),
                });
                const data = await res.json();
                if (data.id) {
                    this.messages.push({
                        id: data.id,
                        body: this.newMsg,
                        is_staff: true,
                        user_name: 'Soporte',
                    });
                    this.newMsg = '';
                    this.scrollDown();
                }
            } catch (e) {}
            this.sending = false;
        },
    }
}
</script>
@endsection
