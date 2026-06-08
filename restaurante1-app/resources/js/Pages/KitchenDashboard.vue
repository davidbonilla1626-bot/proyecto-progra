<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
});

const page = usePage();

// Filter orders into columns
const pendingOrders = computed(() => props.orders.filter(o => o.status === 'Pendiente'));
const preparingOrders = computed(() => props.orders.filter(o => o.status === 'En preparación'));
const readyOrders = computed(() => props.orders.filter(o => o.status === 'Listo para entrega'));
const deliveredOrders = computed(() => props.orders.filter(o => o.status === 'Entregado'));

// Active order detail modal
const selectedOrder = ref(null);
const showModal = ref(false);

const openOrderDetails = (order) => {
    selectedOrder.value = order;
    showModal.value = true;
};

const closeModal = () => {
    selectedOrder.value = null;
    showModal.value = false;
};

// Update status function
const updateStatus = (orderId, newStatus) => {
    router.patch(route('kitchen.updateStatus', orderId), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            if (selectedOrder.value && selectedOrder.value.id === orderId) {
                selectedOrder.value.status = newStatus;
            }
        }
    });
};

// Formatter for currency
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

// Format time
const formatTime = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Panel de Cocina | QuickBite Express" />

    <div class="min-h-screen bg-slate-100 font-['Be_Vietnam_Pro'] text-slate-900 pb-12">
        
        <!-- Header -->
        <header class="bg-white border-b-4 border-slate-900 px-8 py-5 shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto flex justify-between items-center flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-4xl text-red-700 animate-spin">soup_kitchen</span>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black italic tracking-tighter uppercase font-['Epilogue'] text-slate-950">
                            PANEL DE COCINA & FLOW
                        </h1>
                        <p class="text-xs font-black uppercase tracking-widest text-slate-500">Gestión en tiempo real de pedidos activos</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link 
                        v-if="page.props.auth.user.role === 'admin'"
                        :href="route('dashboard')" 
                        class="bg-[#ffcc00] text-slate-950 border-2 border-slate-900 px-5 py-2.5 rounded-xl font-black uppercase text-xs tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                    >
                        Panel Admin
                    </Link>
                    <Link 
                        :href="route('public.menu')" 
                        class="bg-white text-slate-900 border-2 border-slate-900 px-5 py-2.5 rounded-xl font-black uppercase text-xs tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                    >
                        Ver Menú
                    </Link>
                    <Link 
                        :href="route('logout')" 
                        method="post" 
                        as="button" 
                        class="bg-slate-900 hover:bg-slate-800 text-white border-2 border-slate-900 px-5 py-2.5 rounded-xl font-black uppercase text-xs tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer"
                    >
                        Salir
                    </Link>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="max-w-7xl mx-auto px-8 mt-6">
            <div v-if="page.props.flash?.message" class="bg-emerald-100 border-2 border-slate-900 p-4 rounded-xl text-emerald-950 font-bold text-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-6">
                {{ page.props.flash.message }}
            </div>
            <div v-if="page.props.flash?.error" class="bg-red-100 border-2 border-slate-900 p-4 rounded-xl text-red-950 font-bold text-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-6">
                {{ page.props.flash.error }}
            </div>
        </div>

        <!-- Kitchen columns board -->
        <div class="max-w-7xl mx-auto px-8 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
                
                <!-- Column 1: Pendientes (Yellow) -->
                <div class="bg-yellow-50 border-2 border-slate-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col space-y-4">
                    <div class="flex justify-between items-center border-b-2 border-slate-900 pb-2">
                        <span class="font-black italic uppercase font-['Epilogue'] tracking-tight text-yellow-800 text-sm">
                            Pendientes ({{ pendingOrders.length }})
                        </span>
                        <span class="w-3.5 h-3.5 rounded-full bg-yellow-400 border border-slate-900 animate-pulse"></span>
                    </div>

                    <div class="space-y-4 overflow-y-auto max-h-[70vh]">
                        <div v-for="order in pendingOrders" :key="order.id" class="bg-white border-2 border-slate-900 p-4 rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-yellow-400"></div>
                            <div class="flex justify-between items-start pt-2">
                                <span class="font-black font-['Epilogue'] text-sm">{{ order.order_number }}</span>
                                <span class="text-[10px] font-black text-slate-400">{{ formatTime(order.created_at) }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-600 truncate mt-1">
                                {{ order.user?.name || 'Cliente' }}
                            </p>
                            <p class="text-[11px] font-bold text-slate-800 mt-2 bg-slate-50 p-2 rounded-lg border border-slate-200">
                                {{ order.items.length }} plato(s) en cola
                            </p>
                            <div class="flex gap-2 mt-4">
                                <button @click="openOrderDetails(order)" class="w-1/2 bg-slate-900 text-white border border-slate-900 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    Detalle
                                </button>
                                <button @click="updateStatus(order.id, 'En preparación')" class="w-1/2 bg-blue-500 text-white border border-slate-900 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider flex items-center justify-center gap-1">
                                    Preparar <span class="material-symbols-outlined text-[12px] font-bold">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: En preparación (Blue) -->
                <div class="bg-blue-50 border-2 border-slate-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col space-y-4">
                    <div class="flex justify-between items-center border-b-2 border-slate-900 pb-2">
                        <span class="font-black italic uppercase font-['Epilogue'] tracking-tight text-blue-800 text-sm">
                            En preparación ({{ preparingOrders.length }})
                        </span>
                        <span class="w-3.5 h-3.5 rounded-full bg-blue-500 border border-slate-900 animate-pulse"></span>
                    </div>

                    <div class="space-y-4 overflow-y-auto max-h-[70vh]">
                        <div v-for="order in preparingOrders" :key="order.id" class="bg-white border-2 border-slate-900 p-4 rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-blue-500"></div>
                            <div class="flex justify-between items-start pt-2">
                                <span class="font-black font-['Epilogue'] text-sm">{{ order.order_number }}</span>
                                <span class="text-[10px] font-black text-slate-400">{{ formatTime(order.created_at) }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-600 truncate mt-1">
                                {{ order.user?.name || 'Cliente' }}
                            </p>
                            <p class="text-[11px] font-bold text-slate-800 mt-2 bg-slate-50 p-2 rounded-lg border border-slate-200">
                                {{ order.items.length }} plato(s) en cocina
                            </p>
                            <div class="flex gap-2 mt-4">
                                <button @click="openOrderDetails(order)" class="w-1/2 bg-slate-900 text-white border border-slate-900 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    Detalle
                                </button>
                                <button @click="updateStatus(order.id, 'Listo para entrega')" class="w-1/2 bg-purple-500 text-white border border-slate-900 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider flex items-center justify-center gap-1">
                                    Listo <span class="material-symbols-outlined text-[12px] font-bold">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Listos (Listo para entrega) (Purple) -->
                <div class="bg-purple-50 border-2 border-slate-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col space-y-4">
                    <div class="flex justify-between items-center border-b-2 border-slate-900 pb-2">
                        <span class="font-black italic uppercase font-['Epilogue'] tracking-tight text-purple-800 text-sm">
                            Listo para entrega ({{ readyOrders.length }})
                        </span>
                        <span class="w-3.5 h-3.5 rounded-full bg-purple-500 border border-slate-900"></span>
                    </div>

                    <div class="space-y-4 overflow-y-auto max-h-[70vh]">
                        <div v-for="order in readyOrders" :key="order.id" class="bg-white border-2 border-slate-900 p-4 rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-purple-500"></div>
                            <div class="flex justify-between items-start pt-2">
                                <span class="font-black font-['Epilogue'] text-sm">{{ order.order_number }}</span>
                                <span class="text-[10px] font-black text-slate-400">{{ formatTime(order.created_at) }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-600 truncate mt-1">
                                {{ order.user?.name || 'Cliente' }}
                            </p>
                            <p class="text-[11px] font-bold text-slate-800 mt-2 bg-slate-50 p-2 rounded-lg border border-slate-200">
                                Total: {{ formatPrice(order.total) }}
                            </p>
                            <div class="flex gap-2 mt-4">
                                <button @click="openOrderDetails(order)" class="w-1/2 bg-slate-900 text-white border border-slate-900 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    Detalle
                                </button>
                                <button @click="updateStatus(order.id, 'Entregado')" class="w-1/2 bg-emerald-500 text-white border border-slate-900 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider flex items-center justify-center gap-1">
                                    Entregar <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 4: Entregados (Green) -->
                <div class="bg-emerald-50 border-2 border-slate-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col space-y-4">
                    <div class="flex justify-between items-center border-b-2 border-slate-900 pb-2">
                        <span class="font-black italic uppercase font-['Epilogue'] tracking-tight text-emerald-800 text-sm">
                            Entregados hoy ({{ deliveredOrders.length }})
                        </span>
                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 border border-slate-900"></span>
                    </div>

                    <div class="space-y-4 overflow-y-auto max-h-[70vh]">
                        <div v-for="order in deliveredOrders" :key="order.id" class="bg-white border-2 border-slate-900 p-4 rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden opacity-85">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-emerald-500"></div>
                            <div class="flex justify-between items-start pt-2">
                                <span class="font-black font-['Epilogue'] text-sm">{{ order.order_number }}</span>
                                <span class="text-[10px] font-black text-slate-400">{{ formatTime(order.created_at) }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-600 truncate mt-1">
                                {{ order.user?.name || 'Cliente' }}
                            </p>
                            <p class="text-[11px] font-bold text-slate-500 mt-2">
                                Entregado con éxito
                            </p>
                            <div class="mt-4">
                                <button @click="openOrderDetails(order)" class="w-full bg-slate-900 text-white border border-slate-900 px-2 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    Ver Resumen Completo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL DETALLES DEL PEDIDO -->
        <div v-if="showModal && selectedOrder" class="fixed inset-0 bg-slate-950/80 flex items-center justify-center p-4 z-50 animate-fadeIn">
            <div class="bg-white border-4 border-slate-900 rounded-3xl p-6 md:p-8 max-w-xl w-full shadow-[8px_8px_0px_0px_rgba(255,204,0,1)] relative">
                
                <button @click="closeModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-950 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-3xl font-black">close</span>
                </button>

                <div class="border-b-2 border-slate-900 pb-4 mb-6">
                    <h3 class="text-2xl font-black italic uppercase font-['Epilogue'] tracking-tighter text-slate-950">
                        PEDIDO {{ selectedOrder.order_number }}
                    </h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                        Recibido a las {{ new Date(selectedOrder.created_at).toLocaleTimeString() }}
                    </p>
                </div>

                <!-- Detalle Cliente -->
                <div class="bg-slate-50 border-2 border-slate-900 rounded-2xl p-4 mb-6 space-y-1">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">
                        <strong class="font-black text-slate-950">Cliente:</strong> {{ selectedOrder.user?.name || 'N/A' }} ({{ selectedOrder.user?.email || 'N/A' }})
                    </p>
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">
                        <strong class="font-black text-slate-950">Instrucciones y Dirección:</strong>
                    </p>
                    <p class="text-xs font-bold text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200">
                        {{ selectedOrder.notes || 'Sin especificaciones.' }}
                    </p>
                </div>

                <!-- Platos -->
                <div class="space-y-3 max-h-56 overflow-y-auto pr-2">
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-400">Productos a Cocinar</h4>
                    <div v-for="item in selectedOrder.items" :key="item.id" class="flex justify-between items-center bg-slate-50 border border-slate-200 p-3.5 rounded-xl">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-red-700 text-white font-black text-xs flex items-center justify-center border-2 border-slate-900">
                                {{ item.quantity }}
                            </span>
                            <span class="font-black text-sm text-slate-950">{{ item.product?.name || 'Producto' }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase">
                            x {{ item.quantity }}
                        </span>
                    </div>
                </div>

                <!-- Cambiar Estado Selector Directo -->
                <div class="mt-6 border-t-2 border-slate-900 pt-6 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado Actual</p>
                        <select 
                            @change="updateStatus(selectedOrder.id, $event.target.value)"
                            :value="selectedOrder.status"
                            class="text-xs font-black uppercase tracking-wider rounded-lg border-2 border-slate-900 px-3 py-1.5 focus:ring-0 bg-white text-slate-900 cursor-pointer shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                        >
                            <option value="Pendiente">Pendiente</option>
                            <option value="En preparación">En preparación</option>
                            <option value="Listo para entrega">Listo para entrega</option>
                            <option value="Entregado">Entregado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>

                    <button 
                        @click="closeModal"
                        class="bg-slate-950 hover:bg-slate-800 text-white font-black px-6 py-3 rounded-xl text-xs uppercase tracking-widest border-2 border-slate-950 shadow-[4px_4px_0px_0px_rgba(255,204,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all cursor-pointer"
                    >
                        Cerrar Detalle
                    </button>
                </div>

            </div>
        </div>

    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fadeIn {
    animation: fadeIn 0.2s ease-out forwards;
}
</style>
