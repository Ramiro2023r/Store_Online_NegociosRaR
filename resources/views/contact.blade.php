@extends('layouts.app')
@section('title', 'Contáctanos - Negocios RaR')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-2">💬 Contáctanos</h1>
    <p class="text-gray-500 mb-6 text-sm">Chatea con nuestro equipo de soporte.</p>

    <div class="bg-white border rounded-xl flex flex-col h-[500px]" x-data="chat(@json($conversation->messages->map(fn($m) => [
        'id' => $m->id,
        'body' => $m->body,
        'is_staff' => $m->is_staff,
        'user_name' => $m->is_staff ? 'Soporte Negocios RaR' : 'Tú',
    ])))" x-init="initChat()">
        <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="chatbox">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="'flex ' + (msg.is_staff ? 'justify-start' : 'justify-end')">
                    <div :class="'max-w-[75%] rounded-xl px-4 py-2 text-sm ' + (msg.is_staff ? 'bg-gray-100 text-gray-800' : 'bg-rar-600 text-white')">
                        <div class="text-xs opacity-70 mb-1" x-text="msg.user_name"></div>
                        <div x-text="msg.body"></div>
                    </div>
                </div>
            </template>
            <div x-show="messages.length === 0" class="text-center text-gray-400 text-sm py-10">
                Envía tu primer mensaje y nuestro equipo te responderá pronto. 👋
            </div>
        </div>
        <form class="border-t p-3 flex gap-2" @submit.prevent="sendMessage">
            @csrf
            <input type="text" x-model="newMsg" placeholder="Escribe tu mensaje..." required
                class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rar-500">
            <button class="bg-rar-600 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-rar-700" :disabled="sending">Enviar</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 text-sm text-center">
        <div class="bg-white border rounded-xl p-4">📞 (01) 555-0100</div>
        <div class="bg-white border rounded-xl p-4">✉️ ventas@negociosrar.com</div>
        <div class="bg-white border rounded-xl p-4">🕐 Lun-Sáb 9am - 8pm</div>
    </div>
</div>
<script>
function chat(initial) {
    return {
        messages: initial || [],
        newMsg: '',
        sending: false,
        initChat() {
            this.scrollDown();
            setInterval(() => this.poll(), 3000);
        },
        scrollDown() {
            this.$nextTick(() => {
                const box = this.$refs.chatbox;
                if (box) box.scrollTop = box.scrollHeight;
            });
        },
        async poll() {
            const lastId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;
            try {
                const res = await fetch('{{ route("contact.messages") }}?after_id=' + lastId);
                const data = await res.json();
                if (data.length) {
                    data.forEach(m => this.messages.push(m));
                    this.scrollDown();
                }
            } catch (e) {}
        },
        async sendMessage() {
            if (!this.newMsg.trim()) return;
            this.sending = true;
            try {
                const res = await fetch('{{ route("contact.send") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ body: this.newMsg }),
                });
                const data = await res.json();
                if (data.id) {
                    this.messages.push({
                        id: data.id,
                        body: this.newMsg,
                        is_staff: false,
                        user_name: 'Tú',
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
