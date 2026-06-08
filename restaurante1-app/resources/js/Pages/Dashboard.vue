<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Recibimos las estadísticas reales del backend
const props = defineProps({
    stats: {
        type: Object,
        required: true
    }
});

const hoursForm = ref({
    opening_time: props.stats.opening_time || '08:00',
    closing_time: props.stats.closing_time || '22:00'
});

const updateHours = () => {
    router.patch(route('settings.hours'), hoursForm.value, {
        preserveScroll: true
    });
};

const maxWeeklyRevenue = computed(() => {
    if (!props.stats.weekly_sales || props.stats.weekly_sales.length === 0) return 1;
    const max = Math.max(...props.stats.weekly_sales.map(d => d.revenue));
    return max > 0 ? max : 1;
});

// Formateador de moneda profesional
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

// Formatear fecha
const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};

// Concatenar detalles de items
const getOrderDetailsString = (items) => {
    if (!items || items.length === 0) return 'Sin productos';
    return items.map(i => `${i.quantity}x ${i.product?.name || 'Producto'}`).join(', ');
};

// Cambiar estado de pedidos directamente desde el dashboard
const updateOrderStatus = (orderId, newStatus) => {
    router.patch(route('orders.updateStatus', orderId), {
        status: newStatus
    }, {
        preserveScroll: true
    });
};

// Clases de estilo para estados
const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Pendiente':
            return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'En preparación':
            return 'bg-blue-100 text-blue-800 border-blue-300';
        case 'Listo para entrega':
            return 'bg-purple-100 text-purple-800 border-purple-300';
        case 'Entregado':
            return 'bg-green-100 text-green-800 border-green-300';
        case 'Cancelado':
            return 'bg-red-100 text-red-800 border-red-300';
        default:
            return 'bg-slate-100 text-slate-800 border-slate-300';
    }
};

</script>

<template>
    <Head title="Panel de Operaciones | QuickBite Express" />

    <AdminLayout>
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-12 font-['Be_Vietnam_Pro'] text-slate-900">
            
            <!-- HEADER DEL HUB -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 border-b-2 border-slate-200 pb-6">
                <div>
                    <h2 class="text-xl md:text-3xl font-black italic uppercase tracking-tighter text-[#b7102a] font-['Epilogue']">
                        CENTRO DE OPERACIONES
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed font-bold">
                        Monitoreo a alta velocidad para QuickBite Express HQ. Revisa los ingresos de hoy, el flujo en la cocina y gestiona pedidos activos.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="/reports/pdf" target="_blank" class="bg-red-700 text-white border-2 border-slate-900 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] font-bold">picture_as_pdf</span>
                        GENERAR REPORTE PDF
                    </a>
                    <Link :href="route('public.menu')" class="bg-white text-slate-900 border-2 border-slate-900 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all">
                        IR AL MENÚ
                    </Link>
                </div>
            </div>

            <!-- TARJETAS DE ESTADÍSTICAS (Grid de 8 Tarjetas Premium) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tarjeta 1: Ingresos de Hoy -->
                <div class="bg-white border-2 border-slate-900 border-b-8 border-b-[#b7102a] rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-500 tracking-widest uppercase">INGRESOS DE HOY</h3>
                        <div class="w-8 h-8 rounded-full bg-red-100 text-[#b7102a] flex items-center justify-center font-black">
                            <span class="material-symbols-outlined text-[16px]">attach_money</span>
                        </div>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">
                        {{ formatPrice(stats.revenue_today || 0) }}
                    </p>
                    <p class="text-xs font-bold text-slate-400 mt-2">
                        Ventas completadas/entregadas hoy
                    </p>
                </div>

                <!-- Tarjeta 2: Ingresos del Mes -->
                <div class="bg-white border-2 border-slate-900 border-b-8 border-b-emerald-600 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-500 tracking-widest uppercase">VENTAS DEL MES</h3>
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">
                            <span class="material-symbols-outlined text-[16px]">trending_up</span>
                        </div>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">
                        {{ formatPrice(stats.revenue_month || 0) }}
                    </p>
                    <p class="text-xs font-bold text-slate-400 mt-2">
                        Total acumulado este mes
                    </p>
                </div>

                <!-- Tarjeta 3: Pedidos de Hoy -->
                <div class="bg-[#ffcc00] border-2 border-slate-900 border-b-8 border-b-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-slate-950">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-950 tracking-widest uppercase">PEDIDOS DE HOY</h3>
                        <span class="material-symbols-outlined text-slate-950 font-bold">receipt_long</span>
                    </div>
                    <p class="text-4xl font-black text-slate-950 font-['Epilogue']">
                        {{ stats.orders_today || 0 }}
                    </p>
                    <p class="text-xs font-black text-slate-900 uppercase tracking-widest mt-2">
                        Total registrados hoy
                    </p>
                </div>

                <!-- Tarjeta 4: Pedidos Pendientes -->
                <div class="bg-yellow-50 border-2 border-slate-900 border-b-8 border-b-yellow-500 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-slate-900">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-600 tracking-widest uppercase">PEDIDOS PENDIENTES</h3>
                        <span class="material-symbols-outlined text-yellow-600 font-bold">pending_actions</span>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">
                        {{ stats.orders_pending || 0 }}
                    </p>
                    <p class="text-xs font-bold text-slate-500 mt-2">
                        En cola de espera de cocina
                    </p>
                </div>

                <!-- Tarjeta 5: Usuarios Registrados -->
                <div class="bg-white border-2 border-slate-900 border-b-8 border-b-blue-600 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-500 tracking-widest uppercase">CLIENTES ACTIVOS</h3>
                        <span class="material-symbols-outlined text-blue-600 font-bold">group</span>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">
                        {{ stats.registered_users || 0 }}
                    </p>
                    <p class="text-xs font-bold text-slate-400 mt-2">
                        Usuarios tipo cliente en el sistema
                    </p>
                </div>

                <!-- Tarjeta 6: Productos Registrados -->
                <div class="bg-white border-2 border-slate-900 border-b-8 border-b-purple-600 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-500 tracking-widest uppercase">PLATO / MENÚ</h3>
                        <span class="material-symbols-outlined text-purple-600 font-bold">restaurant</span>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">
                        {{ stats.registered_products || 0 }}
                    </p>
                    <p class="text-xs font-bold text-slate-400 mt-2">
                        Platos disponibles en el menú
                    </p>
                </div>

                <!-- Tarjeta 7: Stock Bajo -->
                <div class="bg-orange-100 border-2 border-slate-900 border-b-8 border-b-orange-600 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-slate-900">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-600 tracking-widest uppercase">STOCK BAJO</h3>
                        <span class="material-symbols-outlined text-orange-600 font-bold">warning</span>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">
                        {{ stats.low_stock || 0 }}
                    </p>
                    <p class="text-xs font-bold text-slate-500 mt-2">
                        Productos con &le; 5 unidades
                    </p>
                </div>

                <!-- Tarjeta 8: Productos Agotados -->
                <div class="bg-[#0f172a] border-2 border-slate-900 border-b-8 border-b-[#ffcc00] rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-white">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-400 tracking-widest uppercase">AGOTADOS</h3>
                        <span class="material-symbols-outlined text-red-500 font-bold">block</span>
                    </div>
                    <p class="text-4xl font-black text-white font-['Epilogue']">
                        {{ stats.out_of_stock || 0 }}
                    </p>
                    <p class="text-xs font-bold text-[#ffcc00] mt-2">
                        Productos con stock cero
                    </p>
                </div>
            </div>

            <!-- SECCIÓN: HORARIOS Y ANALÍTICAS AVANZADAS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- PANEL DE HORARIOS (Col-span 1) -->
                <section class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-6">
                            <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg text-red-700">schedule</span> CONFIGURAR HORARIOS
                            </h3>
                        </div>
                        <p class="text-xs text-slate-500 font-bold mb-4">
                            Modifica las horas de apertura y cierre del restaurante. Los clientes no podrán procesar pedidos fuera de estas horas.
                        </p>
                        <form @submit.prevent="updateHours" class="space-y-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Hora de Apertura</label>
                                <input 
                                    v-model="hoursForm.opening_time" 
                                    type="time" 
                                    class="w-full border-2 border-slate-900 rounded-xl px-3 py-2 text-xs font-black" 
                                />
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Hora de Cierre</label>
                                <input 
                                    v-model="hoursForm.closing_time" 
                                    type="time" 
                                    class="w-full border-2 border-slate-900 rounded-xl px-3 py-2 text-xs font-black" 
                                />
                            </div>
                            <button 
                                type="submit" 
                                class="w-full bg-[#ffcc00] hover:bg-[#e6b800] text-slate-950 font-black uppercase text-[11px] tracking-wider py-2.5 px-4 rounded-xl border-2 border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-center block cursor-pointer"
                            >
                                Guardar Horario
                            </button>
                        </form>
                    </div>
                </section>

                <!-- CARD ANALÍTICAS AVANZADAS (Col-span 2) -->
                <section class="bg-[#0f172a] text-white border-2 border-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] lg:col-span-2 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-3 mb-6">
                            <h3 class="text-sm font-black italic uppercase text-amber-400 tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">analytics</span> RENDIMIENTO Y COMPORTAMIENTO
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <!-- Ventas Semanales -->
                            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] font-black text-slate-455 uppercase tracking-wider">VENTAS SEMANALES</p>
                                <p class="text-xl font-black text-white font-['Epilogue'] mt-2">{{ formatPrice(stats.revenue_weekly || 0) }}</p>
                            </div>
                            
                            <!-- Promedio Ticket -->
                            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] font-black text-slate-455 uppercase tracking-wider">TICKET PROMEDIO</p>
                                <p class="text-xl font-black text-white font-['Epilogue'] mt-2">{{ formatPrice(stats.order_average || 0) }}</p>
                            </div>

                            <!-- Hora Pico -->
                            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] font-black text-slate-455 uppercase tracking-wider">HORA PICO</p>
                                <p class="text-xl font-black text-amber-400 font-['Epilogue'] mt-2">{{ stats.busy_hour }}</p>
                            </div>

                            <!-- Día de Más Ventas -->
                            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] font-black text-slate-455 uppercase tracking-wider">DÍA PICO</p>
                                <p class="text-xl font-black text-amber-400 font-['Epilogue'] mt-2">{{ stats.busy_day }}</p>
                            </div>
                        </div>

                        <!-- Clientes más activos preview -->
                        <div class="mt-6 pt-4 border-t border-slate-805">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-wider">Clientes Más Activos</h4>
                                <Link :href="route('ranking.index')" class="text-[10px] font-black text-amber-400 hover:underline uppercase tracking-wider">Ver Leaderboard Completo</Link>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div 
                                    v-for="(c, idx) in stats.active_clients.slice(0, 2)" 
                                    :key="c.id" 
                                    class="bg-slate-900/60 border border-slate-800/80 p-2.5 rounded-xl flex justify-between items-center"
                                >
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-white truncate">{{ c.name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 truncate">{{ c.email }}</p>
                                    </div>
                                    <span class="bg-amber-400/10 text-amber-400 border border-amber-400/20 px-2 py-0.5 rounded text-[10px] font-black shrink-0">
                                        {{ c.orders_count }} PEDIDOS
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- FILA DE REPORTES Y GRÁFICOS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- GRÁFICO DE VENTAS DIARIAS (Col-span 2) -->
                <section class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] lg:col-span-2 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-6">
                        <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">bar_chart</span> VENTAS DIARIAS DE LA ÚLTIMA SEMANA
                        </h3>
                    </div>
                    
                    <div class="h-64 flex items-end gap-3 pt-6 border-b border-slate-200 pl-4 pb-2">
                        <div v-for="day in stats.weekly_sales" :key="day.date" class="flex-1 flex flex-col items-center h-full justify-end group relative cursor-pointer">
                            <!-- Tooltip en hover -->
                            <div class="absolute bottom-full mb-2 bg-slate-950 text-white text-[10px] font-black py-1 px-2.5 rounded-lg border border-slate-900 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap shadow-[2px_2px_0px_0px_rgba(255,204,0,1)] z-25">
                                {{ formatPrice(day.revenue) }}
                            </div>
                            
                            <!-- Barra de Gráfico -->
                            <div 
                                :style="{ height: `${day.revenue > 0 ? (day.revenue / maxWeeklyRevenue * 100) : 0}%` }"
                                class="w-full bg-[#ffcc00] border-2 border-slate-900 rounded-t-lg transition-all duration-300 group-hover:bg-[#b7102a] min-h-[4px]"
                            ></div>
                            
                            <!-- Etiqueta de fecha -->
                            <span class="text-[10px] font-black text-slate-500 mt-2 uppercase tracking-wide">
                                {{ day.date }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- TOP 5 PRODUCTOS MÁS VENDIDOS (Col-span 1) -->
                <section class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col">
                    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
                        <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-red-700">stars</span> TOP 5 PLATO MÁS VENDIDO
                        </h3>
                    </div>

                    <div v-if="!stats.top_products || stats.top_products.length === 0" class="text-center py-12 text-slate-400 font-bold flex-grow flex items-center justify-center">
                        No hay ventas registradas aún.
                    </div>

                    <div v-else class="space-y-4 flex-grow overflow-y-auto">
                        <div v-for="(tp, idx) in stats.top_products" :key="tp.product_id" class="flex items-center justify-between border-b border-dashed border-slate-200 pb-3 last:border-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center border-2 border-slate-900">
                                    {{ idx + 1 }}
                                </span>
                                <div>
                                    <p class="font-black text-xs text-slate-900">{{ tp.product?.name || 'Plato Eliminado' }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">{{ tp.product?.category?.name || 'Menú' }}</p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p class="font-black text-xs text-red-700">{{ formatPrice(tp.total_revenue) }}</p>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ tp.total_sold }} uds.</p>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <!-- SECCIÓN OPERACIONES Y CALIFICACIONES (Dos columnas) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- PEDIDOS RECIENTES EN TIEMPO REAL (Col-span 2) -->
                <section class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
                        <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span> PEDIDOS RECIENTES
                        </h3>
                        <Link :href="route('public.orders')" class="text-xs font-black text-[#b7102a] hover:underline uppercase tracking-wider">Ver Panel Completo</Link>
                    </div>

                    <div class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                        <div v-if="!stats.live_orders || stats.live_orders.length === 0" class="text-center py-12 text-slate-400 font-bold">
                            No hay pedidos activos en este momento.
                        </div>
                        
                        <div v-else class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-[10px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-900 bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-4">ID Pedido</th>
                                        <th class="px-6 py-4">Cliente</th>
                                        <th class="px-6 py-4">Total</th>
                                        <th class="px-6 py-4 text-center">Estado</th>
                                        <th class="px-6 py-4 text-center">Actualizar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 font-bold text-xs">
                                    <tr v-for="order in stats.live_orders.slice(0, 5)" :key="order.id" class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 font-black text-slate-900 font-['Epilogue']">
                                            <Link :href="route('orders.tracking', order.order_number)" class="hover:underline text-red-700">
                                                {{ order.order_number || '#QB-' + order.id }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 text-slate-700">
                                            {{ order.user?.name || 'Cliente' }}
                                        </td>
                                        <td class="px-6 py-4 text-red-700 font-black font-['Epilogue']">{{ formatPrice(order.total) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span :class="`px-2 py-0.5 text-[9px] font-black tracking-widest rounded border-2 ${getStatusBadgeClass(order.status)}`">
                                                {{ order.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <select 
                                                @change="updateOrderStatus(order.id, $event.target.value)"
                                                :value="order.status"
                                                class="text-[10px] font-black uppercase tracking-wider rounded border border-slate-900 px-1 py-0.5 bg-white text-slate-900 cursor-pointer"
                                            >
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="En preparación">En prep.</option>
                                                <option value="Listo para entrega">Listo</option>
                                                <option value="Entregado">Entregado</option>
                                                <option value="Cancelado">Cancelado</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ÚLTIMAS OPINIONES DE CLIENTES (Col-span 1) -->
                <section class="space-y-6">
                    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
                        <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-[#ffcc00] font-bold">reviews</span> OPINIONES RECIENTES
                        </h3>
                    </div>

                    <div v-if="!stats.recent_ratings || stats.recent_ratings.length === 0" class="bg-white border-2 border-slate-900 rounded-2xl p-8 text-center text-slate-400 font-bold shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                        No hay opiniones registradas.
                    </div>

                    <div v-else class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                        <div v-for="rate in stats.recent_ratings" :key="rate.id" class="bg-white border-2 border-slate-900 rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-black text-xs text-slate-950">{{ rate.user?.name || 'Cliente Anónimo' }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">Pedido: {{ rate.order?.order_number }}</p>
                                </div>
                                <div class="flex text-amber-500">
                                    <span v-for="star in 5" :key="star" class="material-symbols-outlined text-[14px]" :class="{ 'fill-current': star <= rate.rating }">
                                        star
                                    </span>
                                </div>
                            </div>
                            <p v-if="rate.comment" class="text-xs text-slate-600 font-medium italic border-t border-dashed border-slate-100 pt-2 leading-relaxed">
                                "{{ rate.comment }}"
                            </p>
                        </div>
                    </div>
                </section>
                
            </div>

            <!-- SECCIÓN PRODUCT MANAGEMENT -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-6">
                    <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest">ACCESOS DIRECTOS DEL MENÚ</h3>
                    <div class="flex gap-2">
                        <Link :href="route('products.index')" class="bg-slate-900 text-white border-2 border-slate-900 px-4 py-2 rounded-lg font-bold text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all font-black">
                            VER TODOS LOS PRODUCTOS
                        </Link>
                        <Link :href="route('products.create')" class="bg-[#ffcc00] text-slate-900 border-2 border-slate-900 px-4 py-2 rounded-lg font-bold text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all font-black">
                            CREAR PRODUCTO
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="border-2 border-slate-900 rounded-2xl overflow-hidden bg-slate-100 h-44 relative group cursor-pointer shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500" alt="Hamburguesas" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent flex items-end p-4">
                            <div>
                                <p class="text-white font-black text-sm leading-tight font-['Epilogue'] uppercase italic">Gestión de Hamburguesas</p>
                                <p class="text-[9px] text-[#ffcc00] font-black uppercase mt-1">Ver Inventario</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-2 border-slate-900 rounded-2xl overflow-hidden bg-slate-100 h-44 relative group cursor-pointer shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <img src="https://images.unsplash.com/photo-1612392062631-94dd858cba88?w=500" alt="Bebidas" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent flex items-end p-4">
                            <div>
                                <p class="text-white font-black text-sm leading-tight font-['Epilogue'] uppercase italic">Gestión de Bebidas</p>
                                <p class="text-[9px] text-[#ffcc00] font-black uppercase mt-1">Ver Inventario</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-2 border-slate-900 rounded-2xl overflow-hidden bg-slate-100 h-44 relative group cursor-pointer shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <img src="https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=500" alt="Pollo" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent flex items-end p-4">
                            <div>
                                <p class="text-white font-black text-sm leading-tight font-['Epilogue'] uppercase italic">Gestión de Alitas y Pollo</p>
                                <p class="text-[9px] text-[#ffcc00] font-black uppercase mt-1">Ver Inventario</p>
                            </div>
                        </div>
                    </div>

                    <Link :href="route('products.create')" class="border-2 border-dashed border-slate-350 rounded-2xl h-44 flex flex-col items-center justify-center text-slate-400 hover:text-slate-900 hover:border-slate-900 hover:bg-slate-50 transition-all cursor-pointer shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)]">
                        <span class="material-symbols-outlined text-4xl mb-2 text-red-700">add_circle</span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-800">Agregar Producto</span>
                    </Link>
                </div>
            </section>

        </div>
    </AdminLayout>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
