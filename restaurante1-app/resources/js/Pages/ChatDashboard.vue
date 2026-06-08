<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onBeforeUnmount } from 'vue';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    chats: Array
});

const selectedOrderId = ref(null);
const activeOrder = ref(null);
const messages = ref([]);
const newMessage = ref('');
const isSending = ref(false);
const chatContainer = ref(null);
let pollInterval = null;

const scrollToBottom = () => {
    setTimeout(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    }, 100);
};

const selectChat = async (orderId) => {
    selectedOrderId.value = orderId;
    try {
        const response = await axios.get(route('chat.orderChat', orderId));
        activeOrder.value = response.data.order;
        messages.value = response.data.messages;
        scrollToBottom();

        // Configurar actualización automática del chat cada 5 segundos
        clearInterval(pollInterval);
        pollInterval = setInterval(async () => {
            if (selectedOrderId.value === orderId) {
                const pollRes = await axios.get(route('chat.orderChat', orderId));
                messages.value = pollRes.data.messages;
            }
        }, 5000);

    } catch (error) {
        console.error('Error cargando chat desde admin:', error);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || isSending.value) return;

    isSending.value = true;
    try {
        const response = await axios.post(route('chat.store'), {
            order_id: selectedOrderId.value,
            message: newMessage.value
        });

        if (response.data.success) {
            messages.value.push(response.data.message);
            newMessage.value = '';
            scrollToBottom();
        }
    } catch (error) {
        console.error('Error respondiendo mensaje:', error);
    } finally {
        isSending.value = false;
    }
};

onBeforeUnmount(() => {
    clearInterval(pollInterval);
});
</script>

<template>
    <Head title="Bandeja de Consultas de Soporte" />

    <AdminLayout>
        <div class="p-8 max-w-7xl mx-auto h-[calc(100vh-40px)] flex flex-col">
            <!-- TITULO -->
            <div class="mb-6 shrink-0">
                <h1 class="text-3xl font-black uppercase tracking-tight italic font-['Epilogue'] text-slate-900">
                    Bandeja de Soporte
                </h1>
                <p class="text-sm font-bold text-slate-500 mt-1">
                    Atiende consultas en tiempo real de clientes con pedidos confirmados o en preparación.
                </p>
            </div>

            <!-- WORKSPACE -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch flex-grow min-h-[480px]">
                <!-- LISTA DE CHATS (5 columnas) -->
                <div class="lg:col-span-4 border-4 border-slate-900 bg-white rounded-3xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-y-auto p-4 flex flex-col gap-3 h-full">
                    <h2 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-slate-100 pb-2">
                        Chats Activos
                    </h2>

                    <div v-if="chats.length === 0" class="text-center py-12">
                        <span class="material-symbols-outlined text-4xl text-slate-300">chat_bubble</span>
                        <p class="text-xs font-black uppercase text-slate-400 mt-2">Sin chats activos</p>
                        <p class="text-[11px] text-slate-500 font-bold mt-1">Los clientes iniciarán chats desde su panel de pedidos.</p>
                    </div>
                    <div v-else class="space-y-2">
                        <button
                            v-for="chat in chats"
                            :key="chat.order_id"
                            @click="selectChat(chat.order_id)"
                            class="w-full text-left p-4 border-4 rounded-2xl transition-all flex justify-between items-center gap-3"
                            :class="selectedOrderId === chat.order_id 
                                ? 'bg-[#ffcc00] border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' 
                                : 'bg-slate-50 border-slate-200 hover:border-slate-900'"
                        >
                            <div class="min-w-0 flex-grow">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400" :class="{'text-slate-700': selectedOrderId === chat.order_id}">
                                    Pedido: {{ chat.order_number }}
                                </p>
                                <p class="font-black text-slate-900 text-sm mt-0.5 truncate">
                                    {{ chat.client_name }}
                                </p>
                                <p class="text-xs text-slate-500 font-bold mt-1 truncate max-w-[180px]">
                                    {{ chat.latest_message }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end shrink-0 gap-1.5">
                                <span class="text-[9px] text-slate-400 font-black uppercase">{{ chat.latest_time }}</span>
                                <span 
                                    v-if="chat.unread_count > 0" 
                                    class="bg-red-700 text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-slate-900"
                                >
                                    {{ chat.unread_count }}
                                </span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- CHAT CONTAINER (8 columnas) -->
                <div class="lg:col-span-8 border-4 border-slate-900 bg-white rounded-3xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col h-full overflow-hidden">
                    <div v-if="!selectedOrderId" class="flex-grow flex flex-col items-center justify-center text-center p-8 bg-slate-50/50">
                        <span class="material-symbols-outlined text-6xl text-slate-300">chat</span>
                        <h3 class="text-lg font-black uppercase text-slate-950 mt-4">Panel de Respuestas</h3>
                        <p class="text-xs text-slate-500 font-bold mt-2 max-w-xs leading-relaxed">
                            Selecciona una conversación del listado izquierdo para comenzar a chatear con el cliente.
                        </p>
                    </div>

                    <div v-else class="flex flex-col h-full">
                        <!-- HEADER -->
                        <div class="bg-slate-950 text-white p-4 flex justify-between items-center border-b-4 border-slate-900 shrink-0">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest text-[#ffcc00]">CHATEANDO CON EL CLIENTE</p>
                                <h3 class="font-black uppercase tracking-tight text-sm font-['Epilogue']">
                                    {{ activeOrder?.client_name }} (Pedido: {{ activeOrder?.order_number }})
                                </h3>
                            </div>
                            <span class="bg-amber-400 text-slate-950 border-2 border-slate-900 px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                {{ activeOrder?.status }}
                            </span>
                        </div>

                        <!-- MENSAJES -->
                        <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 bg-slate-50">
                            <div 
                                v-for="msg in messages" 
                                :key="msg.id"
                                class="flex flex-col max-w-[70%]"
                                :class="msg.sender_id === $page.props.auth.user.id ? 'ml-auto items-end' : 'mr-auto items-start'"
                            >
                                <div 
                                    class="p-3.5 rounded-2xl border-2 border-slate-900 text-xs font-bold shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                                    :class="msg.sender_id === $page.props.auth.user.id 
                                        ? 'bg-[#ffcc00] text-slate-950 rounded-tr-none' 
                                        : 'bg-white text-slate-900 rounded-tl-none'"
                                >
                                    <p class="leading-normal">{{ msg.message }}</p>
                                </div>
                                <span class="text-[9px] text-slate-400 font-black uppercase tracking-wider mt-1 px-1">
                                    {{ msg.sender_id === $page.props.auth.user.id ? 'Tú (Soporte)' : msg.sender?.name || 'Cliente' }}
                                </span>
                            </div>
                        </div>

                        <!-- INPUT -->
                        <div class="p-4 border-t-4 border-slate-900 bg-white shrink-0">
                            <form @submit.prevent="sendMessage" class="flex gap-3">
                                <input
                                    v-model="newMessage"
                                    type="text"
                                    placeholder="Escribe una respuesta para el cliente..."
                                    class="flex-grow border-2 border-slate-900 rounded-xl px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#ffcc00] placeholder-slate-400"
                                />
                                <button
                                    type="submit"
                                    :disabled="!newMessage.trim() || isSending"
                                    class="bg-[#ffcc00] hover:bg-[#e6b800] text-slate-950 font-black uppercase text-[11px] tracking-wider py-3 px-5 rounded-xl border-2 border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span>Responder</span>
                                    <span class="material-symbols-outlined text-sm font-bold">send</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
