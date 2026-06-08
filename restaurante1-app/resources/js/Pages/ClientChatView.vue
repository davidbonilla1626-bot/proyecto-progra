<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import PublicHeader from '@/Components/PublicHeader.vue';

const props = defineProps({
    orders: Array
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

const selectOrder = async (orderId) => {
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
        console.error('Error cargando chat:', error);
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
        console.error('Error enviando mensaje:', error);
    } finally {
        isSending.value = false;
    }
};

onBeforeUnmount(() => {
    clearInterval(pollInterval);
});
</script>

<template>
    <Head title="Soporte y Consultas de Pedidos" />

    <div class="min-h-screen bg-[#f4f4f0] text-slate-900 pb-20 font-['Be_Vietnam_Pro'] selection:bg-[#ffcc00] selection:text-slate-900">
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-6xl mx-auto px-6 mt-12">
            <!-- TITULO -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-black uppercase tracking-tighter italic font-['Epilogue'] text-slate-950">
                    Soporte de Pedidos
                </h1>
                <p class="font-bold text-slate-500 mt-2">
                    Comunícate directamente con nuestro personal sobre el estado, ingredientes o entrega de tus pedidos.
                </p>
            </div>

            <!-- CHAT WORKSPACE -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch h-[600px]">
                <!-- LISTA DE PEDIDOS IZQUIERDA (5 columnas) -->
                <div class="lg:col-span-4 border-4 border-slate-900 bg-white rounded-3xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-y-auto p-4 flex flex-col gap-3 h-full">
                    <h2 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-slate-100 pb-2">
                        Mis Pedidos Recientes
                    </h2>

                    <div v-if="orders.length === 0" class="text-center py-12">
                        <span class="material-symbols-outlined text-4xl text-slate-350">receipt</span>
                        <p class="text-xs font-black uppercase text-slate-400 mt-2">Sin pedidos para chatear</p>
                        <p class="text-[11px] text-slate-500 font-bold mt-1">Realiza un pedido para habilitar el chat de soporte.</p>
                    </div>
                    <div v-else class="space-y-2">
                        <button
                            v-for="order in orders"
                            :key="order.id"
                            @click="selectOrder(order.id)"
                            class="w-full text-left p-4 border-4 rounded-2xl transition-all flex justify-between items-center gap-3"
                            :class="selectedOrderId === order.id 
                                ? 'bg-[#ffcc00] border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' 
                                : 'bg-slate-50 border-slate-200 hover:border-slate-900'"
                        >
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400" :class="{'text-slate-700': selectedOrderId === order.id}">
                                    {{ order.created_at }}
                                </p>
                                <p class="font-black text-slate-900 text-sm mt-0.5">
                                    {{ order.order_number }}
                                </p>
                                <p class="text-[11px] font-black uppercase tracking-widest text-red-700 mt-1">
                                    {{ order.status }}
                                </p>
                            </div>
                            <span 
                                v-if="order.unread_count > 0" 
                                class="bg-red-700 text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-slate-900 shrink-0"
                            >
                                {{ order.unread_count }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- CHAT ACTIVE PANEL DERECHA (8 columnas) -->
                <div class="lg:col-span-8 border-4 border-slate-900 bg-white rounded-3xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col h-full overflow-hidden">
                    <div v-if="!selectedOrderId" class="flex-grow flex flex-col items-center justify-center text-center p-8 bg-slate-50/50">
                        <span class="material-symbols-outlined text-6xl text-slate-300">forum</span>
                        <h3 class="text-lg font-black uppercase text-slate-950 mt-4">Bandeja de Consultas</h3>
                        <p class="text-xs text-slate-500 font-bold mt-2 max-w-xs leading-relaxed">
                            Selecciona uno de tus pedidos del listado de la izquierda para abrir el chat directo con cocina y soporte.
                        </p>
                    </div>

                    <div v-else class="flex flex-col h-full">
                        <!-- HEADER CHAT -->
                        <div class="bg-slate-950 text-white p-4 flex justify-between items-center border-b-4 border-slate-900 shrink-0">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest text-amber-400">CHAT SOPORTE ACTIVO</p>
                                <h3 class="font-black uppercase tracking-tight text-sm font-['Epilogue']">
                                    Pedido: {{ activeOrder?.order_number }}
                                </h3>
                            </div>
                            <span class="bg-red-700 text-white border-2 border-white px-3 py-1 rounded-full text-[10px] font-black uppercase">
                                {{ activeOrder?.status }}
                            </span>
                        </div>

                        <!-- MENSAJES -->
                        <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 bg-slate-50">
                            <div v-if="messages.length === 0" class="text-center py-12 text-slate-400 font-bold text-xs">
                                No hay mensajes anteriores. ¡Escribe un mensaje para iniciar la consulta!
                            </div>
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
                                    {{ msg.sender_id === $page.props.auth.user.id ? 'Tú' : msg.sender?.name || 'Personal' }}
                                </span>
                            </div>
                        </div>

                        <!-- INPUT FORM -->
                        <div class="p-4 border-t-4 border-slate-900 bg-white shrink-0">
                            <form @submit.prevent="sendMessage" class="flex gap-3">
                                <input
                                    v-model="newMessage"
                                    type="text"
                                    placeholder="Escribe tu consulta aquí..."
                                    class="flex-grow border-2 border-slate-900 rounded-xl px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#ffcc00] placeholder-slate-400"
                                />
                                <button
                                    type="submit"
                                    :disabled="!newMessage.trim() || isSending"
                                    class="bg-[#ffcc00] hover:bg-[#e6b800] text-slate-950 font-black uppercase text-[11px] tracking-wider py-3 px-5 rounded-xl border-2 border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span>Enviar</span>
                                    <span class="material-symbols-outlined text-sm font-bold">send</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
