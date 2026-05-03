<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Mock data para las órdenes activas
const liveOrders = [
    { id: '#QB-4902', customer: 'Marcus Vane', details: '2x Monster Burger, 1x Chili Fries', status: 'PREPARING', statusColor: 'text-[#b7102a] border-[#b7102a] bg-red-50' },
    { id: '#QB-4903', customer: 'Sarah Jenkins', details: '1x Vegan Combo, Large Cola', status: 'READY', statusColor: 'text-green-600 border-green-600 bg-green-50' },
    { id: '#QB-4904', customer: 'David Miller', details: '3x Cheesy Wraps, 3x Onion Rings', status: 'OUT', statusColor: 'text-slate-500 border-slate-500 bg-slate-50' },
    { id: '#QB-4905', customer: 'Elena K.', details: '1x Breakfast Melt, Hot Latte', status: 'PREPARING', statusColor: 'text-[#b7102a] border-[#b7102a] bg-red-50' }
];

// Mock data para productos
const dashboardProducts = [
    { id: 1, name: 'The Megabyte Burger', img: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500', isBestSeller: true },
    { id: 2, name: 'Supersonic Dog', img: 'https://images.unsplash.com/photo-1612392062631-94dd858cba88?w=500', isBestSeller: false },
    { id: 3, name: 'Nitro Tenders', img: 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=500', isBestSeller: false }
];
</script>

<template>
    <Head title="QuickBite Express | Admin Hub" />

    <AdminLayout>
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-12">
            
            <!-- HEADER DEL HUB -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-black italic uppercase tracking-tighter text-[#b7102a] font-['Epilogue']">
                        OPERATIONS HUB
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        High-velocity monitoring for QuickBite Express HQ. Manage inventory, track live heat in the kitchen, and optimize delivery performance.
                    </p>
                </div>
                <button class="bg-white text-slate-900 border-2 border-slate-900 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest flex items-center gap-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all shrink-0">
                    <span class="material-symbols-outlined text-[18px]">download</span> EXPORT DATA
                </button>
            </div>

            <!-- TARJETAS DE ESTADÍSTICAS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tarjeta 1: Revenue -->
                <div class="bg-white border-2 border-slate-900 border-b-8 border-b-[#b7102a] rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,0.05)]">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-500 tracking-widest uppercase">DAILY REVENUE</h3>
                        <div class="w-8 h-8 rounded-full bg-red-100 text-[#b7102a] flex items-center justify-center font-black">
                            <span class="material-symbols-outlined text-[16px]">attach_money</span>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900 font-['Epilogue']">$12,482</p>
                    <p class="text-xs font-bold text-emerald-500 mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">trending_up</span> +14.2% from yesterday
                    </p>
                </div>

                <!-- Tarjeta 2: Orders -->
                <div class="bg-[#ffcc00] border-2 border-slate-900 border-b-8 border-b-slate-900 rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-900 tracking-widest uppercase">ORDERS TO GRILL</h3>
                        <span class="material-symbols-outlined text-slate-900">outdoor_grill</span>
                    </div>
                    <p class="text-4xl font-black text-slate-900 font-['Epilogue']">42</p>
                    <p class="text-xs font-black text-slate-900 uppercase tracking-widest mt-2">KITCHEN AT FULL CAPACITY</p>
                </div>

                <!-- Tarjeta 3: Delivery -->
                <div class="bg-[#0f172a] border-2 border-slate-900 border-b-8 border-b-[#ffcc00] rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-white">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xs font-black text-slate-400 tracking-widest uppercase">AVG. DELIVERY</h3>
                        <span class="material-symbols-outlined text-[#ffcc00]">timer</span>
                    </div>
                    <p class="text-3xl font-black text-white font-['Epilogue']">18m 42s</p>
                    <p class="text-xs font-bold text-[#ffcc00] mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">task_alt</span> Under target goal
                    </p>
                </div>
            </div>

            <!-- SECCIÓN LIVE ORDERS -->
            <section>
                <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-6">
                    <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> LIVE ACTIVE ORDERS
                    </h3>
                    <a href="#" class="text-xs font-bold text-[#b7102a] hover:underline">View All Kitchen Log</a>
                </div>

                <div class="bg-white border-2 border-slate-900 rounded-xl overflow-hidden shadow-[6px_6px_0px_0px_rgba(0,0,0,0.05)]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-200">
                                <tr>
                                    <th class="px-6 py-4">ORDER ID</th>
                                    <th class="px-6 py-4">CUSTOMER</th>
                                    <th class="px-6 py-4">ITEM DETAILS</th>
                                    <th class="px-6 py-4 text-center">STATUS</th>
                                    <th class="px-6 py-4 text-center">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <tr v-for="order in liveOrders" :key="order.id" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-black text-slate-900">{{ order.id }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ order.customer }}</td>
                                    <td class="px-6 py-4 text-slate-600 truncate max-w-[200px]">{{ order.details }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span :class="`px-3 py-1 text-[9px] font-black tracking-widest rounded-full border-2 ${order.statusColor}`">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-slate-400 hover:text-slate-900 transition-colors">
                                            <span class="material-symbols-outlined">more_vert</span>
                                        </button>
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
                    <h3 class="text-sm font-black italic uppercase text-slate-900 tracking-widest">PRODUCT MANAGEMENT</h3>
                    <Link :href="route('products.create')" class="bg-[#ffcc00] text-slate-900 border-2 border-slate-900 px-4 py-2 rounded-lg font-bold text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                        ADD NEW ITEM
                    </Link>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Productos estáticos de ejemplo -->
                    <div v-for="prod in dashboardProducts" :key="prod.id" class="border-2 border-slate-900 rounded-xl overflow-hidden bg-slate-100 h-40 relative group cursor-pointer">
                        <img :src="prod.img" :alt="prod.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent flex items-end p-4">
                            <p class="text-white font-black text-sm leading-tight font-['Epilogue']">{{ prod.name }}</p>
                        </div>
                        <div v-if="prod.isBestSeller" class="absolute top-2 left-2 bg-[#b7102a] text-white px-2 py-0.5 text-[8px] font-black uppercase tracking-widest border border-slate-900">
                            BEST SELLER
                        </div>
                    </div>

                    <!-- Botón de añadir fantasma -->
                    <Link :href="route('products.create')" class="border-2 border-dashed border-slate-300 rounded-xl h-40 flex flex-col items-center justify-center text-slate-400 hover:text-slate-900 hover:border-slate-900 hover:bg-slate-50 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-4xl mb-2">add_circle</span>
                        <span class="text-[10px] font-black uppercase tracking-widest">New Product</span>
                    </Link>
                </div>
            </section>

        </div>
    </AdminLayout>
</template>
