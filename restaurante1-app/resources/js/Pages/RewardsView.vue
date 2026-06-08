<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';

const props = defineProps({
    points: Number,
    transactions: Array
});

const page = usePage();

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const redeemReward = (rewardType) => {
    router.post(route('rewards.redeem'), {
        reward_type: rewardType
    }, {
        onSuccess: () => {
            // Se actualiza el estado automáticamente
        }
    });
};
</script>

<template>
    <Head title="Mis Recompensas y Fidelidad" />

    <div class="min-h-screen bg-[#f4f4f0] text-slate-900 pb-20 font-['Be_Vietnam_Pro'] selection:bg-[#ffcc00] selection:text-slate-900">
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-6xl mx-auto px-6 mt-12">
            <!-- TITULO PRINCIPAL -->
            <div class="border-4 border-slate-900 bg-[#ffcc00] p-8 rounded-3xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] mb-12 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tighter italic font-['Epilogue'] text-slate-950">
                            Club QuickBite
                        </h1>
                        <p class="font-bold text-slate-900 mt-2 max-w-xl text-sm md:text-base leading-relaxed">
                            ¡Cada $1 de compra te otorga 1 punto de fidelidad! Canjea tus puntos acumulados por deliciosas recompensas gratis o cupones de descuento.
                        </p>
                    </div>
                    <div class="bg-white border-4 border-slate-900 p-6 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] shrink-0 text-center min-w-[200px]">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-500">Mis Puntos Disponibles</p>
                        <p class="text-4xl md:text-5xl font-black text-red-700 italic mt-1 font-['Epilogue']">
                            {{ points }}
                        </p>
                        <p class="text-[11px] font-black uppercase text-slate-800 mt-1">Puntos de fidelidad</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- SECCIÓN IZQUIERDA: CANJE DE RECOMPENSAS -->
                <div class="lg:col-span-2 space-y-6">
                    <h2 class="text-2xl font-black uppercase italic tracking-tight font-['Epilogue'] text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[28px] text-red-700">redeem</span>
                        Recompensas Disponibles
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- BEBIDA GRATIS -->
                        <div class="border-4 border-slate-900 bg-white rounded-2xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 bg-blue-100 rounded-xl border-2 border-slate-900 flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-blue-700 font-bold">local_drink</span>
                                </div>
                                <h3 class="text-lg font-black uppercase text-slate-950 leading-tight">Bebida Gratis</h3>
                                <p class="text-xs text-slate-600 font-bold mt-2">
                                    Canjea una bebida o refresco de tu elección valorado en hasta $3.00.
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t-2 border-slate-100">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-black text-slate-500 uppercase">Costo</span>
                                    <span class="text-sm font-black text-red-700 uppercase">50 Puntos</span>
                                </div>
                                <button
                                    @click="redeemReward('drink')"
                                    :disabled="points < 50"
                                    class="w-full text-center py-2.5 px-4 rounded-xl border-2 border-slate-900 font-black uppercase text-[11px] tracking-wider transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5"
                                    :class="points >= 50 ? 'bg-[#ffcc00] hover:bg-[#e6b800] text-slate-950 cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                >
                                    Canjear
                                </button>
                            </div>
                        </div>

                        <!-- PAPAS FRITAS -->
                        <div class="border-4 border-slate-900 bg-white rounded-2xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-xl border-2 border-slate-900 flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-amber-600 font-bold">tapas</span>
                                </div>
                                <h3 class="text-lg font-black uppercase text-slate-950 leading-tight">Papas Fritas</h3>
                                <p class="text-xs text-slate-600 font-bold mt-2">
                                    Una porción crujiente de nuestras papas fritas rústicas valoradas en $4.00.
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t-2 border-slate-100">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-black text-slate-500 uppercase">Costo</span>
                                    <span class="text-sm font-black text-red-700 uppercase">80 Puntos</span>
                                </div>
                                <button
                                    @click="redeemReward('fries')"
                                    :disabled="points < 80"
                                    class="w-full text-center py-2.5 px-4 rounded-xl border-2 border-slate-900 font-black uppercase text-[11px] tracking-wider transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5"
                                    :class="points >= 80 ? 'bg-[#ffcc00] hover:bg-[#e6b800] text-slate-950 cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                >
                                    Canjear
                                </button>
                            </div>
                        </div>

                        <!-- DESCUENTO ESPECIAL -->
                        <div class="border-4 border-slate-900 bg-white rounded-2xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 bg-emerald-100 rounded-xl border-2 border-slate-900 flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-emerald-700 font-bold">payments</span>
                                </div>
                                <h3 class="text-lg font-black uppercase text-slate-950 leading-tight">Descuento de $10</h3>
                                <p class="text-xs text-slate-600 font-bold mt-2">
                                    Cupón de descuento directo de $10.00 aplicable en cualquier pedido de comida.
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t-2 border-slate-100">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-black text-slate-500 uppercase">Costo</span>
                                    <span class="text-sm font-black text-red-700 uppercase">120 Puntos</span>
                                </div>
                                <button
                                    @click="redeemReward('discount')"
                                    :disabled="points < 120"
                                    class="w-full text-center py-2.5 px-4 rounded-xl border-2 border-slate-900 font-black uppercase text-[11px] tracking-wider transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5"
                                    :class="points >= 120 ? 'bg-[#ffcc00] hover:bg-[#e6b800] text-slate-950 cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                >
                                    Canjear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN DERECHA: HISTORIAL DE TRANSACCIONES -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-black uppercase italic tracking-tight font-['Epilogue'] text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[28px] text-red-700">history</span>
                        Historial de Puntos
                    </h2>

                    <div class="border-4 border-slate-900 bg-white rounded-2xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] max-h-[480px] overflow-y-auto">
                        <div v-if="transactions.length === 0" class="text-center py-8">
                            <span class="material-symbols-outlined text-4xl text-slate-300">receipt</span>
                            <p class="text-xs font-black uppercase text-slate-400 mt-2">Sin transacciones aún</p>
                            <p class="text-xs text-slate-500 font-bold mt-1">Realiza pedidos para comenzar a acumular puntos.</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="t in transactions"
                                :key="t.id"
                                class="flex justify-between items-start gap-4 p-3 border-2 border-slate-100 rounded-xl hover:border-slate-300 transition-colors"
                            >
                                <div class="flex-grow">
                                    <p class="text-xs font-black text-slate-900 leading-snug">
                                        {{ t.description }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1">
                                        {{ formatDate(t.created_at) }}
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span
                                        class="inline-block px-2.5 py-1 rounded-lg border-2 border-slate-900 text-xs font-black uppercase"
                                        :class="t.type === 'earned' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                    >
                                        {{ t.type === 'earned' ? '+' : '-' }}{{ t.points }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
