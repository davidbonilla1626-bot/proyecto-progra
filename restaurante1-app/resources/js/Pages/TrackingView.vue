<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    estimatedTime: {
        type: String,
        default: '20 minutos'
    }
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleString('es-ES', { 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
};

// Progression stages mapping
const stages = ['Pendiente', 'En preparación', 'Listo para entrega', 'Entregado'];

const currentStageIndex = computed(() => {
    if (props.order.status === 'Cancelado') return -1;
    return stages.indexOf(props.order.status);
});

// Color classes based on status
const getStatusColorClass = (status) => {
    switch (status) {
        case 'Pendiente':
            return 'bg-yellow-400 text-yellow-950 border-yellow-500';
        case 'En preparación':
            return 'bg-blue-500 text-white border-blue-600';
        case 'Listo para entrega':
            return 'bg-purple-500 text-white border-purple-600';
        case 'Entregado':
            return 'bg-emerald-500 text-white border-emerald-600';
        case 'Cancelado':
            return 'bg-red-500 text-white border-red-600';
        default:
            return 'bg-slate-500 text-white border-slate-600';
    }
};

const getProgressPercent = computed(() => {
    if (props.order.status === 'Cancelado') return 0;
    const index = currentStageIndex.value;
    if (index === -1) return 0;
    return (index / (stages.length - 1)) * 100;
});
</script>

<template>
    <Head :title="`Seguimiento ${order.order_number} | QuickBite Express`" />

    <div class="bg-slate-50 text-slate-900 min-h-screen font-['Be_Vietnam_Pro'] pb-20">
        
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-3xl mx-auto px-6 pt-12">
            
            <!-- Tracking Card -->
            <div class="bg-white border-4 border-slate-900 rounded-3xl p-6 md:p-10 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden space-y-8">
                
                <!-- Status Banner Accent -->
                <div class="absolute top-0 left-0 right-0 h-3" :class="{
                    'bg-yellow-400 animate-pulse': order.status === 'Pendiente',
                    'bg-blue-500 animate-pulse': order.status === 'En preparación',
                    'bg-purple-500': order.status === 'Listo para entrega',
                    'bg-emerald-500': order.status === 'Entregado',
                    'bg-red-500': order.status === 'Cancelado'
                }"></div>

                <!-- Header Info -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b-2 border-slate-200 pb-6">
                    <div>
                        <span class="text-xs font-black uppercase tracking-widest text-slate-400">Seguimiento de Pedido</span>
                        <h1 class="text-3xl font-black italic uppercase tracking-tighter text-slate-950 font-['Epilogue'] mt-1">
                            {{ order.order_number }}
                        </h1>
                        <p class="text-xs font-bold text-slate-500 mt-1">Realizado el {{ formatDate(order.created_at) }}</p>
                    </div>

                    <div class="flex flex-col items-start md:items-end">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Estado de preparación</span>
                        <span class="px-4 py-2 rounded-xl border-2 border-slate-900 text-sm font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mt-1.5"
                              :class="getStatusColorClass(order.status)">
                            {{ order.status }}
                        </span>
                    </div>
                </div>

                <!-- Estimated Delivery / Cancelled Block -->
                <div v-if="order.status !== 'Cancelado'" class="bg-slate-900 text-white rounded-2xl p-6 border-2 border-slate-950 flex items-center justify-between shadow-[4px_4px_0px_0px_rgba(239,68,68,0.2)]">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-[#ffcc00] animate-bounce">alarm</span>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tiempo estimado de entrega</p>
                            <p class="text-2xl font-black text-[#ffcc00] font-['Epilogue'] uppercase">{{ estimatedTime }}</p>
                        </div>
                    </div>
                    <div class="hidden sm:block text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Servicio</p>
                        <p class="text-xs font-bold text-white uppercase tracking-wider">QuickBite Express</p>
                    </div>
                </div>

                <div v-else class="bg-red-50 border-2 border-red-500 text-red-950 rounded-2xl p-6 flex items-center gap-4">
                    <span class="material-symbols-outlined text-4xl text-red-600">error</span>
                    <div>
                        <p class="text-lg font-black font-['Epilogue'] uppercase">Pedido Cancelado</p>
                        <p class="text-sm font-semibold">Lo sentimos, este pedido ha sido cancelado y su inventario se ha liberado.</p>
                    </div>
                </div>

                <!-- PROGRESS BAR IN REAL-TIME -->
                <div v-if="order.status !== 'Cancelado'" class="space-y-6">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400">Progreso del Pedido</h3>
                    
                    <div class="relative pt-4 pb-8">
                        <!-- Line Backing -->
                        <div class="absolute top-[28px] left-[5%] right-[5%] h-2 bg-slate-200 rounded-full z-0"></div>
                        
                        <!-- Colored Dynamic progress line -->
                        <div class="absolute top-[28px] left-[5%] h-2 rounded-full z-0 transition-all duration-700 ease-out"
                             :style="{ width: `${getProgressPercent * 0.9}%` }"
                             :class="{
                                 'bg-yellow-400': order.status === 'Pendiente',
                                 'bg-blue-500': order.status === 'En preparación',
                                 'bg-purple-500': order.status === 'Listo para entrega',
                                 'bg-emerald-500': order.status === 'Entregado',
                             }"
                        ></div>

                        <!-- Progress Steps -->
                        <div class="relative z-10 flex justify-between">
                            <div v-for="(stage, idx) in stages" :key="stage" class="flex flex-col items-center">
                                <!-- Dot -->
                                <div class="w-8 h-8 rounded-full border-2 border-slate-900 flex items-center justify-center font-black transition-colors duration-500"
                                     :class="[
                                         idx <= currentStageIndex
                                             ? getStatusColorClass(stages[idx])
                                             : 'bg-white text-slate-300 border-slate-200'
                                     ]"
                                >
                                    <span class="material-symbols-outlined text-[16px] font-bold">
                                        {{ idx === 0 ? 'pending' : idx === 1 ? 'soup_kitchen' : idx === 2 ? 'sports_motorsports' : 'task_alt' }}
                                    </span>
                                </div>
                                <!-- Text -->
                                <span class="text-[9px] font-black uppercase tracking-widest mt-3 whitespace-nowrap text-center"
                                      :class="idx <= currentStageIndex ? 'text-slate-950 font-black' : 'text-slate-400 font-semibold'"
                                >
                                    {{ stage }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items list summary -->
                <div class="space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 pb-2">Resumen de Compra</h3>
                    
                    <div class="space-y-3">
                        <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center bg-slate-50 border border-slate-200 p-3.5 rounded-2xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-200 border border-slate-350 overflow-hidden shrink-0">
                                    <img :src="item.product?.image || item.product?.image_path" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-black text-sm text-slate-950">{{ item.product?.name || 'Producto' }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">{{ item.quantity }} unidades x {{ formatPrice(item.price) }}</p>
                                </div>
                            </div>
                            <span class="font-black text-sm text-red-700 font-['Epilogue']">
                                {{ formatPrice(item.price * item.quantity) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Row -->
                <div class="border-t-2 border-slate-900 pt-6 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Pagado</p>
                        <p class="text-3xl font-black text-red-700 font-['Epilogue']">{{ formatPrice(order.total) }}</p>
                    </div>

                    <div class="flex gap-2">
                        <a :href="`/orders/${order.id}/ticket`" target="_blank" class="bg-white hover:bg-slate-50 text-slate-900 font-black px-4 py-3 rounded-xl text-xs uppercase tracking-widest border-2 border-slate-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">receipt</span> Ver Ticket
                        </a>
                        <Link :href="route('public.menu')" class="bg-slate-900 hover:bg-slate-800 text-white font-black px-4 py-3 rounded-xl text-xs uppercase tracking-widest border-2 border-slate-900 shadow-[3px_3px_0px_0px_rgba(255,204,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">restaurant_menu</span> Volver al menú
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
