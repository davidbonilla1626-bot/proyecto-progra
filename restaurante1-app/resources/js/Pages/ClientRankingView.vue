<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    clients: Array
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

const getTierBadgeClass = (level) => {
    switch (level) {
        case 'Platino':
            return 'bg-purple-100 text-purple-800 border-purple-400';
        case 'Oro':
            return 'bg-amber-100 text-amber-800 border-amber-400';
        case 'Plata':
            return 'bg-slate-100 text-slate-700 border-slate-400';
        default:
            return 'bg-orange-50 text-orange-700 border-orange-300';
    }
};

const getRankIcon = (index) => {
    if (index === 0) return '🏆';
    if (index === 1) return '🥈';
    if (index === 2) return '🥉';
    return `#${index + 1}`;
};
</script>

<template>
    <Head title="Ranking de Clientes Fieles" />

    <AdminLayout>
        <div class="p-8 max-w-7xl mx-auto">
            <!-- HEADER -->
            <div class="mb-8">
                <h1 class="text-3xl font-black uppercase tracking-tight italic font-['Epilogue'] text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[32px] text-amber-500">leaderboard</span>
                    Ranking de Clientes Fieles
                </h1>
                <p class="text-sm font-bold text-slate-500 mt-1">
                    Listado de clientes ordenado por el volumen total de compras entregadas y nivel de fidelización.
                </p>
            </div>

            <!-- LEADERBOARD TABLE -->
            <div class="border-4 border-slate-900 bg-white rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b-4 border-slate-900 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <th class="p-4 text-center w-20">Puesto</th>
                                <th class="p-4">Cliente</th>
                                <th class="p-4 text-center">Pedidos Entregados</th>
                                <th class="p-4">Total Invertido</th>
                                <th class="p-4">Puntos Acumulados</th>
                                <th class="p-4">Nivel Fidelización</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-slate-100 text-xs font-bold text-slate-700">
                            <tr v-if="clients.length === 0">
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    No hay registros de clientes válidos en el sistema.
                                </td>
                            </tr>
                            <tr 
                                v-for="(client, index) in clients" 
                                :key="client.id"
                                class="hover:bg-slate-50 transition-colors"
                                :class="{'bg-yellow-50/40': index === 0}"
                            >
                                <!-- PUESTO -->
                                <td class="p-4 text-center font-black text-sm whitespace-nowrap">
                                    <span :class="{'text-2xl': index < 3}">
                                        {{ getRankIcon(index) }}
                                    </span>
                                </td>
                                <!-- CLIENTE -->
                                <td class="p-4 whitespace-nowrap">
                                    <div>
                                        <p class="font-black text-slate-900 text-xs leading-tight">
                                            {{ client.name }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-bold mt-0.5">
                                            {{ client.email }}
                                        </p>
                                    </div>
                                </td>
                                <!-- PEDIDOS -->
                                <td class="p-4 text-center whitespace-nowrap">
                                    <span class="inline-block bg-slate-100 border border-slate-350 px-2.5 py-1 rounded-lg text-xs font-black text-slate-800">
                                        {{ client.orders_count }} pedidos
                                    </span>
                                </td>
                                <!-- TOTAL GASTADO -->
                                <td class="p-4 whitespace-nowrap font-black text-slate-900 text-sm">
                                    {{ formatPrice(client.total_spent) }}
                                </td>
                                <!-- PUNTOS -->
                                <td class="p-4 whitespace-nowrap font-mono text-red-700 font-black text-sm">
                                    ★ {{ client.points }}
                                </td>
                                <!-- NIVEL -->
                                <td class="p-4 whitespace-nowrap">
                                    <span 
                                        class="inline-block px-3 py-1 rounded-full border-2 text-[10px] font-black uppercase tracking-wider shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]"
                                        :class="getTierBadgeClass(client.level)"
                                    >
                                        {{ client.level }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
