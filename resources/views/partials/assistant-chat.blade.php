@auth
<div x-data="assistantChat()"
     x-init="init()"
     class="fixed bottom-4 right-4 z-50"
     x-cloak>
    {{-- Toggle button --}}
    <button @click="open = !open"
            class="w-14 h-14 rounded-full bg-rar-600 text-white shadow-lg hover:bg-rar-700 flex items-center justify-center transition"
            :class="{ 'ring-2 ring-rar-300': open }">
        <svg x-show="!open" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <svg x-show="open" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    {{-- Chat panel --}}
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="absolute bottom-16 right-0 w-80 sm:w-96 h-[32rem] bg-white rounded-2xl shadow-2xl border flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="bg-rar-600 text-white px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                <span class="font-semibold text-sm">Asistente RaR</span>
            </div>
            <div class="flex items-center gap-1">
                <button @click="newConversation()" class="p-1 hover:bg-rar-500 rounded" title="Nueva conversación">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
        </div>

        {{-- Conversation list / Messages --}}
        <template x-if="!currentConversation">
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                <template x-for="conv in conversations" :key="conv.id">
                    <div @click="loadConversation(conv.id)"
                         class="p-3 rounded-xl border hover:bg-gray-50 cursor-pointer text-sm flex items-center justify-between">
                        <span x-text="conv.title" class="truncate"></span>
                        <button @click.stop="deleteConversation(conv.id)" class="text-gray-400 hover:text-red-500 shrink-0 ml-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
                <template x-if="conversations.length === 0">
                    <div class="text-center text-gray-400 py-8 text-sm">
                        <p class="mb-2">No hay conversaciones aún.</p>
                        <button @click="newConversation()" class="text-rar-600 font-semibold hover:underline">Iniciar una</button>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="currentConversation">
            <div class="flex-1 flex flex-col min-h-0">
                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-3 space-y-3" x-ref="messagesContainer">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.role === 'user' ? 'text-right' : ''">
                            <div :class="msg.role === 'user'
                                ? 'bg-rar-600 text-white rounded-2xl rounded-br-sm px-4 py-2 inline-block max-w-[85%] text-sm'
                                : 'bg-gray-100 rounded-2xl rounded-bl-sm px-4 py-2 inline-block max-w-[85%] text-sm text-left'">
                                <p class="whitespace-pre-wrap" x-text="msg.content"></p>
                            </div>
                            <p class="text-xs text-gray-400 mt-1" x-text="timeAgo(msg.created_at)"></p>
                        </div>
                    </template>
                    <div x-show="loading" class="text-left">
                        <div class="bg-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 inline-block">
                            <div class="flex gap-1">
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Back button + Input --}}
                <div class="border-t p-3 space-y-2 shrink-0">
                    <button @click="backToList()" class="text-xs text-gray-400 hover:text-gray-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Volver a conversaciones
                    </button>
                    <form @submit.prevent="sendMessage()" class="flex gap-2">
                        <input type="text" x-model="newMessage" placeholder="Escribe un mensaje..." maxlength="4000"
                               class="flex-1 border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rar-500 focus:outline-none"
                               :disabled="loading">
                        <button type="submit" :disabled="!newMessage.trim() || loading"
                                class="bg-rar-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-rar-700 disabled:opacity-50 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function assistantChat() {
    return {
        open: false,
        conversations: [],
        currentConversation: null,
        messages: [],
        newMessage: '',
        loading: false,

        async init() {
            await this.loadConversations();
        },

        async loadConversations() {
            try {
                const res = await fetch('/asistente/conversaciones');
                this.conversations = await res.json();
            } catch (e) {
                console.error('Error loading conversations', e);
            }
        },

        async newConversation() {
            try {
                const res = await fetch('/asistente/conversaciones', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                    body: JSON.stringify({}),
                });
                const conv = await res.json();
                this.conversations.unshift(conv);
                this.currentConversation = conv.id;
                this.messages = [];
            } catch (e) {
                console.error('Error creating conversation', e);
            }
        },

        async loadConversation(id) {
            this.currentConversation = id;
            try {
                const res = await fetch('/asistente/conversaciones/' + id);
                const data = await res.json();
                this.messages = data.messages || [];
            } catch (e) {
                console.error('Error loading conversation', e);
            }
        },

        async deleteConversation(id) {
            if (!confirm('¿Eliminar esta conversación?')) return;
            try {
                await fetch('/asistente/conversaciones/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content } });
                this.conversations = this.conversations.filter(c => c.id !== id);
                if (this.currentConversation === id) this.backToList();
            } catch (e) {
                console.error('Error deleting conversation', e);
            }
        },

        backToList() {
            this.currentConversation = null;
            this.messages = [];
        },

        async sendMessage() {
            const msg = this.newMessage.trim();
            if (!msg || !this.currentConversation || this.loading) return;

            this.messages.push({ id: 'temp', role: 'user', content: msg, created_at: new Date().toISOString() });
            this.newMessage = '';
            this.loading = true;

            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch('/asistente/conversaciones/' + this.currentConversation + '/mensaje', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                    body: JSON.stringify({ message: msg }),
                });
                const data = await res.json();

                if (data.content) {
                    this.messages.push({ id: 'resp-' + Date.now(), role: 'assistant', content: data.content, created_at: new Date().toISOString() });
                }

                this.$nextTick(() => this.scrollToBottom());
            } catch (e) {
                console.error('Error sending message', e);
            } finally {
                this.loading = false;
            }
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) container.scrollTop = container.scrollHeight;
        },

        timeAgo(date) {
            if (!date) return '';
            const d = new Date(date);
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);
            if (diff < 60) return 'ahora';
            if (diff < 3600) return Math.floor(diff / 60) + 'm';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h';
            return d.toLocaleDateString();
        }
    };
}
</script>
@endauth
