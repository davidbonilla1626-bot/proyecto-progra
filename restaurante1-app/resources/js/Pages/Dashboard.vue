<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Recibimos las estadísticas reales del backend
const props = defineProps({
    stats: {
        type: Object,
        required: true
    }
});

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
            return 'bg-orange-100 text-orange-800 border-orange-300';
        case 'Listo para entrega':
            return 'bg-blue-100 text-blue-800 border-blue-300';
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
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-12">
            
            <!-- HEADER DEL HUB -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
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

            <!-- TARJETAS DE ESTADÍSTICAS -->
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

                <!-- Tarjeta 2: Pedidos de Hoy -->
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

                <!-- Tarjeta 3: Stock Bajo -->
                <div class="bg-orange-100 border-2 border-slate-900 border-b-8 border-b-orange-600 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-slate-900">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-600 tracking-widest uppercase">STOCK BAJO</h3>
                        <span class="material-symbols-outlined text-orange-600 font-bold">warning</span>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">
                        {{ stats.low_stock || 0 }}
                    </p>
                    <p class="text-xs font-bold text-slate-500 mt-2">
                        Productos con &le; 3 unidades
                    </p>
                </div>

                <!-- Tarjeta 4: Productos Agotados -->
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

            <!-- GRÁFICO DE VENTAS DIARIAS DE LA ÚLTIMA SEMANA -->
            <section class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
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

            <!-- SECCIÓN LIVE ORDERS -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-6">
                    <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span> PEDIDOS RECIENTES EN TIEMPO REAL
                    </h3>
                    <Link :href="route('public.orders')" class="text-xs font-black text-[#b7102a] hover:underline uppercase tracking-wider">Ver Panel Completo de Pedidos</Link>
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
                                    <th class="px-6 py-4">Productos</th>
                                    <th class="px-6 py-4 text-center">Total</th>
                                    <th class="px-6 py-4 text-center">Estado</th>
                                    <th class="px-6 py-4 text-center">Actualizar Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-bold">
                                <tr v-for="order in stats.live_orders" :key="order.id" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-black text-slate-900 font-['Epilogue']">{{ order.order_number || '#QB-' + order.id }}</td>
                                    <td class="px-6 py-4 text-slate-700">
                                        {{ order.user?.name || 'Cliente QuickBite' }}
                                        <p class="text-[10px] text-slate-400 font-medium">{{ order.user?.email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 truncate max-w-[250px] font-medium">{{ getOrderDetailsString(order.items) }}</td>
                                    <td class="px-6 py-4 text-center text-red-700 font-black font-['Epilogue']">{{ formatPrice(order.total) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span :class="`px-3 py-1 text-[9px] font-black tracking-widest rounded-lg border-2 ${getStatusBadgeClass(order.status)}`">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <select 
                                            @change="updateOrderStatus(order.id, $event.target.value)"
                                            :value="order.status"
                                            class="text-[10px] font-black uppercase tracking-wider rounded-lg border border-slate-900 px-2 py-1 bg-white text-slate-900 cursor-pointer"
                                        >
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="En preparación">En preparación</option>
                                            <option value="Listo para entrega">Listo para entrega</option>
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

            <!-- SECCIÓN PRODUCT MANAGEMENT -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-6">
                    <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest">ACCESOS DIRECTOS DEL MENÚ</h3>
                    <div class="flex gap-2">
                        <Link :href="route('products.index')" class="bg-slate-900 text-white border-2 border-slate-900 px-4 py-2 rounded-lg font-bold text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                            VER TODOS LOS PRODUCTOS
                        </Link>
                        <Link :href="route('products.create')" class="bg-[#ffcc00] text-slate-900 border-2 border-slate-900 px-4 py-2 rounded-lg font-bold text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                            CREAR PRODUCTO
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Productos estáticos de ejemplo con links a editar -->
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

                    <!-- Botón de añadir fantasma -->
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
