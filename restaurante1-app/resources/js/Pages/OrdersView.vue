<script setup>
import { Head, router, usePage, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Recibimos los datos del backend
const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    },
    isAdmin: {
        type: Boolean,
        default: false
    }
});

const page = usePage();

// Estado del Modal de Detalles del Pedido
const selectedOrder = ref(null);
const showDetailsModal = ref(false);

const openOrderDetails = (order) => {
    selectedOrder.value = order;
    showDetailsModal.value = true;
};

const closeOrderDetails = () => {
    showDetailsModal.value = false;
    selectedOrder.value = null;
};

// Formateador de moneda profesional
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

// Formateador de fechas amable
const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleString('es-ES', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
};

// Cambiar estado de pedidos (Solo Admin)
const updateOrderStatus = (orderId, newStatus) => {
    router.patch(route('orders.updateStatus', orderId), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Si el modal está abierto con este pedido, actualizamos el estado en el modal también
            if (selectedOrder.value && selectedOrder.value.id === orderId) {
                selectedOrder.value.status = newStatus;
            }
        }
    });
};

// Clases de estilo para los estados de los pedidos
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

// Iconos para cada estado
const getStatusIcon = (status) => {
    switch (status) {
        case 'Pendiente': return 'pending';
        case 'En preparación': return 'soup_kitchen';
        case 'Listo para entrega': return 'sports_motorsports';
        case 'Entregado': return 'task_alt';
        case 'Cancelado': return 'cancel';
        default: return 'help';
    }
};

// Resumen rápido de estados para el admin
const pendingCount = computed(() => props.orders.filter(o => o.status === 'Pendiente').length);
const preparingCount = computed(() => props.orders.filter(o => o.status === 'En preparación').length);
const readyCount = computed(() => props.orders.filter(o => o.status === 'Listo para entrega').length);

</script>

<template>
    <Head title="Mis Pedidos | QuickBite Express" />

    <!-- ============================================== -->
    <!-- 1. VISTA DEL ADMINISTRADOR                     -->
    <!-- ============================================== -->
    <AdminLayout v-if="isAdmin">
        <Head title="Control de Pedidos | Panel Administrativo" />
        
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-10">
            
            <!-- Encabezado del Dashboard de Pedidos -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-3xl font-black italic uppercase tracking-tighter text-red-700 font-['Epilogue'] flex items-center gap-2">
                        <span class="material-symbols-outlined text-3xl">receipt_long</span>
                        CONTROL DE PEDIDOS
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed font-bold">
                        Monitor de pedidos en tiempo real. Gestiona la preparación, despacho y finalización de los pedidos del restaurante.
                    </p>
                </div>
            </div>

            <!-- Mini estadísticas rápidas de cocina -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white border-2 border-slate-900 p-5 rounded-2xl flex items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NUEVOS EN COLA</p>
                        <p class="text-3xl font-black font-['Epilogue'] text-slate-900 mt-1">{{ pendingCount }}</p>
                    </div>
                    <span class="material-symbols-outlined text-yellow-500 text-4xl font-bold">schedule</span>
                </div>
                <div class="bg-[#ffcc00] border-2 border-slate-900 p-5 rounded-2xl flex items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-slate-950">
                    <div>
                        <p class="text-[10px] font-black text-slate-800 uppercase tracking-widest">EN PREPARACIÓN</p>
                        <p class="text-3xl font-black font-['Epilogue'] mt-1">{{ preparingCount }}</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-950 text-4xl font-bold">soup_kitchen</span>
                </div>
                <div class="bg-red-700 border-2 border-slate-900 p-5 rounded-2xl flex items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-white">
                    <div>
                        <p class="text-[10px] font-black text-red-200 uppercase tracking-widest">LISTOS PARA ENTREGA</p>
                        <p class="text-3xl font-black font-['Epilogue'] mt-1">{{ readyCount }}</p>
                    </div>
                    <span class="material-symbols-outlined text-[#ffcc00] text-4xl font-bold">sports_motorsports</span>
                </div>
            </div>

            <!-- Tabla de Pedidos Activos (Diseño Brutalista) -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <div v-if="orders.length === 0" class="text-center py-20">
                    <span class="material-symbols-outlined text-5xl text-slate-300">receipt_long</span>
                    <p class="text-slate-400 text-lg font-black mt-2">No hay pedidos registrados en el sistema.</p>
                </div>
                
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        
                        <!-- Cabecera de la tabla -->
                        <thead class="text-[11px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-900 bg-slate-50">
                            <tr>
                                <th class="px-6 py-5">N° Pedido</th>
                                <th class="px-6 py-5">Cliente</th>
                                <th class="px-6 py-5">Fecha / Hora</th>
                                <th class="px-6 py-5">Total</th>
                                <th class="px-6 py-5">Estado</th>
                                <th class="px-6 py-5 text-right font-bold">Detalle / Gestión</th>
                            </tr>
                        </thead>
                        
                        <!-- Cuerpo de la tabla -->
                        <tbody class="divide-y divide-slate-200 font-bold">
                            <tr v-for="order in orders" :key="order.id" class="hover:bg-yellow-50 transition-colors">
                                
                                <!-- ID -->
                                <td class="px-6 py-4 font-black text-slate-900 font-['Epilogue']">{{ order.order_number || '#QB-' + order.id }}</td>
                                
                                <!-- Cliente -->
                                <td class="px-6 py-4 text-slate-700">
                                    {{ order.user?.name || 'Cliente QuickBite' }}
                                    <p class="text-[10px] text-slate-400 font-medium">{{ order.user?.email }}</p>
                                </td>
                                
                                <!-- Fecha -->
                                <td class="px-6 py-4 text-slate-500 font-bold text-xs">{{ formatDate(order.created_at) }}</td>
                                
                                <!-- Total -->
                                <td class="px-6 py-4 font-black text-red-700 font-['Epilogue']">{{ formatPrice(order.total) }}</td>
                                
                                <!-- Estado Selector -->
                                <td class="px-6 py-4">
                                    <select 
                                        @change="updateOrderStatus(order.id, $event.target.value)"
                                        :value="order.status"
                                        class="text-xs font-black uppercase tracking-wider rounded-lg border-2 border-slate-900 px-3 py-1.5 focus:ring-0 bg-white text-slate-900 cursor-pointer shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-slate-50"
                                    >
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="En preparación">En preparación</option>
                                        <option value="Listo para entrega">Listo para entrega</option>
                                        <option value="Entregado">Entregado</option>
                                        <option value="Cancelado">Cancelado</option>
                                    </select>
                                </td>
                                
                                <!-- Acciones -->
                                <td class="px-6 py-4 text-right">
                                    <button 
                                        @click="openOrderDetails(order)"
                                        class="bg-slate-900 text-white border-2 border-slate-900 hover:bg-slate-800 px-4 py-2 rounded-xl text-xs uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all font-black"
                                    >
                                        VER PRODUCTOS
                                    </button>
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>

    <!-- ============================================== -->
    <!-- 2. VISTA DEL CLIENTE                           -->
    <!-- ============================================== -->
    <div v-else class="bg-slate-50 min-h-screen font-['Be_Vietnam_Pro'] text-slate-900 pb-20">
        
        <!-- Navbar público -->
        <PublicHeader />

        <main class="max-w-4xl mx-auto px-6 mt-12 space-y-12">
            
            <!-- Encabezado de sección -->
            <header class="border-b-2 border-slate-200 pb-6">
                <h1 class="text-4xl md:text-5xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] leading-none">
                    MIS <span class="text-red-700">PEDIDOS</span>
                </h1>
                <p class="text-slate-600 font-bold mt-2">Sigue el estado de tus delicias en tiempo real.</p>
            </header>

            <!-- Estado Vacío -->
            <div v-if="orders.length === 0" class="bg-white border-2 border-slate-900 rounded-3xl p-12 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 animate-pulse">receipt_long</span>
                <p class="text-slate-500 font-black text-xl">¿Aún no has pedido?</p>
                <p class="text-sm text-slate-400 mt-2">Nuestras hamburguesas, alitas y malteadas están esperándote.</p>
                <Link :href="route('public.menu')" class="inline-block mt-6 bg-[#ffcc00] border-2 border-slate-900 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest text-slate-950 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all">Hacer mi primer pedido</Link>
            </div>

            <!-- Lista de tarjetas de pedidos pasados/activos -->
            <div v-else class="space-y-6">
                
                <div v-for="order in orders" :key="order.id" class="bg-white border-2 border-slate-900 rounded-3xl p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
                    
                    <!-- Decoración lateral activa: Se muestra color según el estado -->
                    <div class="absolute left-0 top-0 bottom-0 w-2" :class="{
                        'bg-yellow-400 animate-pulse': order.status === 'Pendiente',
                        'bg-blue-500 animate-pulse': order.status === 'En preparación',
                        'bg-purple-500': order.status === 'Listo para entrega',
                        'bg-green-500': order.status === 'Entregado',
                        'bg-red-500': order.status === 'Cancelado'
                    }"></div>

                    <!-- Detalles -->
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        
                        <!-- Bloque Izquierdo: ID, Estado y Lista de Productos -->
                        <div class="space-y-4 flex-grow">
                            <div class="flex items-center gap-3">
                                <span class="font-black text-xl font-['Epilogue'] text-slate-950">Pedido {{ order.order_number || '#QB-' + order.id }}</span>
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ formatDate(order.created_at) }}</span>
                            </div>
                            
                            <!-- Badge de Estado -->
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border-2 border-slate-900 font-black text-xs uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                                 :class="getStatusBadgeClass(order.status)">
                                <span class="material-symbols-outlined text-[15px] font-bold">
                                    {{ getStatusIcon(order.status) }}
                                </span>
                                {{ order.status }}
                            </div>

                            <!-- Resumen de items -->
                            <div class="border-t border-slate-100 pt-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Artículos en la bolsa</p>
                                <ul class="space-y-2">
                                    <li v-for="item in order.items" :key="item.id" class="text-sm font-bold text-slate-700 flex justify-between items-center gap-2 border-b border-dashed border-slate-100 pb-1">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 bg-red-700 rounded-full"></span> 
                                            <span>{{ item.quantity }}x {{ item.product?.name || 'Producto' }}</span>
                                            <span class="text-xs text-slate-400 font-normal">({{ formatPrice(item.price) }} c/u)</span>
                                        </div>
                                        <span class="text-slate-900 font-black">Subtotal: {{ formatPrice(item.price * item.quantity) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Bloque Derecho: Precio Total y Botón de Acción -->
                        <div class="flex flex-col items-start md:items-end justify-between border-t border-slate-200 md:border-t-0 pt-4 md:pt-0 shrink-0">
                            <div class="text-left md:text-right mb-4 md:mb-0">
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Pagado</p>
                                <p class="text-3xl font-black text-red-700 font-['Epilogue']">{{ formatPrice(order.total) }}</p>
                            </div>

                            <!-- Botón Ver Detalles completo -->
                            <button 
                                @click="openOrderDetails(order)"
                                class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(239,68,68,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all flex items-center gap-2 cursor-pointer"
                            >
                                <span class="material-symbols-outlined text-[16px]">info</span>
                                VER DETALLES
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- ============================================== -->
    <!-- MODAL DETALLES DEL PEDIDO (CLIENTE Y ADMIN)    -->
    <!-- ============================================== -->
    <div v-if="showDetailsModal && selectedOrder" class="fixed inset-0 bg-slate-950/80 flex items-center justify-center p-4 z-50 animate-fadeIn">
        <div class="bg-white border-4 border-slate-900 rounded-3xl p-6 md:p-8 max-w-xl w-full shadow-[8px_8px_0px_0px_rgba(255,204,0,1)] relative animate-scaleUp">
            
            <!-- Botón Cerrar -->
            <button @click="closeOrderDetails" class="absolute top-4 right-4 text-slate-400 hover:text-slate-950 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-3xl font-black">close</span>
            </button>

            <!-- Encabezado Modal -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6">
                <h3 class="text-2xl font-black italic uppercase font-['Epilogue'] tracking-tighter text-slate-950">
                    DETALLE DEL PEDIDO {{ selectedOrder.order_number || '#QB-' + selectedOrder.id }}
                </h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                    {{ formatDate(selectedOrder.created_at) }}
                </p>
            </div>

            <!-- Información Cliente y Envíos (Especialmente útil en el Admin) -->
            <div class="bg-slate-50 border-2 border-slate-900 rounded-2xl p-4 mb-6 space-y-2">
                <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">
                    <strong class="font-black text-slate-950">Cliente:</strong> {{ selectedOrder.user?.name || 'Cliente Registrado' }} ({{ selectedOrder.user?.email }})
                </p>
                <div class="text-xs font-bold text-slate-700 uppercase tracking-wide leading-relaxed">
                    <strong class="font-black text-slate-950">Datos Adicionales / Dirección:</strong> 
                    <p class="text-slate-600 mt-1 lowercase font-medium bg-white p-2 border border-slate-200 rounded-lg first-letter:uppercase">
                        {{ selectedOrder.notes || 'No se ingresaron notas especiales.' }}
                    </p>
                </div>
            </div>

            <!-- Lista de items detallados -->
            <div class="space-y-4 max-h-60 overflow-y-auto pr-2">
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 pb-2">Artículos del Pedido</h4>
                <div v-for="item in selectedOrder.items" :key="item.id" class="flex justify-between items-center bg-white border border-slate-200 p-3 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-slate-100 border border-slate-950 rounded-lg overflow-hidden shrink-0">
                            <img :src="item.product?.image || item.product?.image_path" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-black text-sm text-slate-950 leading-none">{{ item.product?.name || 'Producto' }}</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-black">CANTIDAD: {{ item.quantity }}</p>
                        </div>
                    </div>
                    <span class="font-black text-sm text-red-700 font-['Epilogue']">
                        {{ formatPrice(item.price * item.quantity) }}
                    </span>
                </div>
            </div>

            <!-- Total y Cierre -->
            <div class="border-t-2 border-slate-900 pt-6 mt-6 flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Monto Total</p>
                    <p class="text-3xl font-black text-red-700 font-['Epilogue']">{{ formatPrice(selectedOrder.total) }}</p>
                </div>
                <button 
                    @click="closeOrderDetails"
                    class="bg-slate-950 hover:bg-slate-800 text-white font-black px-6 py-3 rounded-xl text-xs uppercase tracking-widest border-2 border-slate-950 shadow-[4px_4px_0px_0px_rgba(255,204,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all cursor-pointer"
                >
                    CERRAR
                </button>
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

@keyframes scaleUp {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fadeIn {
    animation: fadeIn 0.25s ease-out forwards;
}

.animate-scaleUp {
    animation: scaleUp 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
