<script setup>
/**
 * IMPORTACIONES Y DEPENDENCIAS
 * Head: Para cambiar el título de la página en la pestaña del navegador.
 * usePage: Hook de Inertia para acceder a variables globales (como el usuario autenticado).
 * PublicHeader / AdminLayout: Componentes de diseño dependiendo de quién vea la página.
 */
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed, ref } from 'vue';

/**
 * ESTADO DEL USUARIO
 * Extraemos la información del usuario actual (nombre, email, id) desde Inertia.
 */
const page = usePage();
const user = computed(() => page.props.auth.user);

/**
 * VERIFICACIÓN DE ROL (ADMIN VS CLIENTE)
 * Aquí decidimos qué pantalla mostrar. Como no tenemos una tabla de roles todavía,
 * hacemos una validación temporal: Si el ID es 1 o el correo contiene "david", es Admin.
 */
const isAdmin = computed(() => {
    if (!user.value) return false;
    return user.value.id === 1 || user.value.email.includes('david');
});

/**
 * DATOS SIMULADOS (MOCK DATA) PARA EL CLIENTE
 * Representa el historial de pedidos que un usuario normal vería.
 * En un sistema real, esto vendría del controlador a través de un defineProps().
 */
const customerOrders = ref([
    {
        id: 'ORD-8921',
        date: 'Hoy, 14:30',
        status: 'En cocina', // Estados posibles: 'En cocina', 'En camino', 'Completado'
        total: 22.50,
        items: ['1x Hamburguesa Gran Megabyte', '1x Papas Overclocked', '1x Cola Clásica Legacy']
    },
    {
        id: 'ORD-8104',
        date: 'Hace 3 días',
        status: 'Completado',
        total: 18.99,
        items: ['1x Nitro Chilli Dog', '1x Cerveza Artesanal IP-A']
    }
]);

/**
 * DATOS SIMULADOS (MOCK DATA) PARA EL ADMINISTRADOR
 * Representa la cola de pedidos entrantes en tiempo real para la cocina/cajero.
 */
const adminIncomingOrders = ref([
    {
        id: 'ORD-8921',
        customer: 'Juan Pérez',
        time: 'Hace 5 min',
        total: 22.50,
        status: 'pending' // pending, completed, cancelled
    },
    {
        id: 'ORD-8922',
        customer: 'María Silva',
        time: 'Hace 2 min',
        total: 14.00,
        status: 'pending'
    }
]);

/**
 * FUNCIONES DEL PANEL DE ADMINISTRADOR
 * Permiten cambiar visualmente el estado del pedido en la tabla (de pendiente a completado o cancelado).
 */
const markCompleted = (id) => {
    const order = adminIncomingOrders.value.find(o => o.id === id);
    if(order) order.status = 'completed';
};

const markCancelled = (id) => {
    const order = adminIncomingOrders.value.find(o => o.id === id);
    if(order) order.status = 'cancelled';
};
</script>

<template>
    <Head title="QuickBite Express | Orders" />

    <!-- ============================================== -->
    <!-- 1. VISTA DEL ADMINISTRADOR / CAJERO            -->
    <!-- ============================================== -->
    <!-- Se muestra SOLO si isAdmin es verdadero -->
    <AdminLayout v-if="isAdmin">
        
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-10">
            
            <!-- Encabezado del Dashboard de Pedidos -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-black italic uppercase tracking-tighter text-red-700 font-['Epilogue'] flex items-center gap-2">
                        <span class="material-symbols-outlined text-3xl">receipt_long</span>
                        INCOMING ORDERS
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        Monitor de pedidos en tiempo real. Marca los pedidos como completados cuando estén listos para entregar.
                    </p>
                </div>
            </div>

            <!-- Tabla de Pedidos Activos (Diseño Brutalista) -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,0.05)]">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        
                        <!-- Cabecera de la tabla -->
                        <thead class="text-[11px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-900 bg-slate-50">
                            <tr>
                                <th class="px-6 py-5">ORDER ID</th>
                                <th class="px-6 py-5">CUSTOMER</th>
                                <th class="px-6 py-5">TIME</th>
                                <th class="px-6 py-5">TOTAL</th>
                                <th class="px-6 py-5 text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        
                        <!-- Cuerpo de la tabla con los pedidos entrantes -->
                        <tbody class="divide-y divide-slate-200 font-medium">
                            <tr v-for="order in adminIncomingOrders" :key="order.id" class="hover:bg-yellow-50 transition-colors" :class="{'opacity-50': order.status !== 'pending'}">
                                
                                <!-- Información básica -->
                                <td class="px-6 py-4 font-black text-slate-900 font-['Epilogue']">{{ order.id }}</td>
                                <td class="px-6 py-4 font-bold text-slate-700">{{ order.customer }}</td>
                                <td class="px-6 py-4 text-slate-500 text-xs font-bold">{{ order.time }}</td>
                                <td class="px-6 py-4 font-black text-red-700 font-['Epilogue']">${{ order.total.toFixed(2) }}</td>
                                
                                <!-- Columna de Acciones -->
                                <td class="px-6 py-4 text-right">
                                    <!-- Si está pendiente, mostramos los botones de Completar y Cancelar -->
                                    <div v-if="order.status === 'pending'" class="flex justify-end gap-2">
                                        <button @click="markCompleted(order.id)" class="bg-green-500 text-white p-2 rounded-lg border-2 border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-y-[1px] hover:translate-x-[1px] transition-all" title="Mark Completed">
                                            <span class="material-symbols-outlined text-[16px] font-bold">check</span>
                                        </button>
                                        <button @click="markCancelled(order.id)" class="bg-red-700 text-white p-2 rounded-lg border-2 border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-y-[1px] hover:translate-x-[1px] transition-all" title="Cancel Order">
                                            <span class="material-symbols-outlined text-[16px] font-bold">close</span>
                                        </button>
                                    </div>
                                    <!-- Si ya se completó o canceló, mostramos el texto del estado -->
                                    <div v-else>
                                        <span class="text-xs font-black uppercase tracking-widest" :class="order.status === 'completed' ? 'text-green-600' : 'text-red-700'">
                                            {{ order.status }}
                                        </span>
                                    </div>
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>


    <!-- ============================================== -->
    <!-- 2. VISTA DEL CLIENTE (USUARIO NORMAL)          -->
    <!-- ============================================== -->
    <!-- Se muestra si isAdmin es falso -->
    <div v-else class="bg-slate-50 min-h-screen font-['Be_Vietnam_Pro'] text-slate-900 pb-20">
        
        <!-- Navbar público -->
        <PublicHeader />

        <!-- Contenedor central (max-w-4xl para que las tarjetas no sean exageradamente anchas) -->
        <main class="max-w-4xl mx-auto px-6 mt-12 space-y-12">
            
            <!-- Encabezado de sección -->
            <header>
                <h1 class="text-4xl md:text-5xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] leading-none">
                    YOUR <span class="text-red-700">ORDERS</span>
                </h1>
                <p class="text-slate-600 font-bold mt-2">Track your active food or reorder your favorites.</p>
            </header>

            <!-- Lista de tarjetas de pedidos pasados/activos -->
            <div class="space-y-6">
                
                <div v-for="order in customerOrders" :key="order.id" class="bg-white border-2 border-slate-900 rounded-3xl p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,0.05)] relative overflow-hidden">
                    
                    <!-- Decoración lateral (Línea amarilla): Solo se muestra si el pedido NO está completado (indicando que está activo) -->
                    <div v-if="order.status !== 'Completado'" class="absolute left-0 top-0 bottom-0 w-2 bg-yellow-400"></div>

                    <!-- Estructura Flexbox para dividir Detalles (Izquierda) y Resumen/Botón (Derecha) -->
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        
                        <!-- Bloque Izquierdo: ID, Estado y Lista de Productos -->
                        <div class="space-y-4">
                            <!-- ID del pedido y Fecha -->
                            <div class="flex items-center gap-3">
                                <span class="font-black text-xl font-['Epilogue']">{{ order.id }}</span>
                                <span class="text-xs font-black text-slate-500 uppercase tracking-widest">{{ order.date }}</span>
                            </div>
                            
                            <!-- Tracking Badge (Insignia dinámica de estado) -->
                            <!-- Cambia de color e icono dependiendo si está Completado, En cocina, etc. -->
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border-2 border-slate-900 font-black text-xs uppercase tracking-widest"
                                 :class="order.status === 'Completado' ? 'bg-slate-100 text-slate-600' : 'bg-yellow-400 text-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]'">
                                <span class="material-symbols-outlined text-[14px]">
                                    {{ order.status === 'Completado' ? 'task_alt' : (order.status === 'En cocina' ? 'local_fire_department' : 'directions_bike') }}
                                </span>
                                {{ order.status }}
                            </div>

                            <!-- Lista de artículos comprados en ese pedido -->
                            <ul class="space-y-1">
                                <li v-for="item in order.items" :key="item" class="text-sm font-bold text-slate-600 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-red-700 rounded-full"></span> {{ item }}
                                </li>
                            </ul>
                        </div>

                        <!-- Bloque Derecho: Precio Total y Botón de Acción -->
                        <div class="flex flex-col items-start md:items-end justify-between border-t border-slate-200 md:border-t-0 pt-4 md:pt-0">
                            <!-- Total -->
                            <div class="text-left md:text-right mb-4 md:mb-0">
                                <p class="text-xs font-black text-slate-500 uppercase tracking-widest">Total Paid</p>
                                <p class="text-2xl font-black text-red-700 font-['Epilogue']">${{ order.total.toFixed(2) }}</p>
                            </div>

                            <!-- Botón de REORDER (Para pedir exactamente lo mismo) -->
                            <button class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(183,16,42,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                                REORDER
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
