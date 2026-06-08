<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    logs: Object // Paginated object
});

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
};
</script>

<template>
    <Head title="Registro de Auditoría Administrativa" />

    <AdminLayout>
        <div class="p-8 max-w-7xl mx-auto">
            <!-- TITULO Y ACCIONES -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-black uppercase tracking-tight italic font-['Epilogue'] text-slate-900">
                        Auditoría del Sistema
                    </h1>
                    <p class="text-sm font-bold text-slate-500 mt-1">
                        Historial cronológico de cambios de productos, categorías, estados de pedidos y roles.
                    </p>
                </div>
                <div class="bg-white border-2 border-slate-900 px-4 py-2 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] text-xs font-black uppercase tracking-widest text-slate-700 shrink-0">
                    Total Eventos: {{ logs.total }}
                </div>
            </div>

            <!-- TABLA DE LOGS -->
            <div class="border-4 border-slate-900 bg-white rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b-4 border-slate-900 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <th class="p-4">Fecha y Hora</th>
                                <th class="p-4">Usuario Responsable</th>
                                <th class="p-4">Acción Realizada</th>
                                <th class="p-4">Dirección IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-slate-100 text-xs font-bold text-slate-700">
                            <tr v-if="logs.data.length === 0">
                                <td colspan="4" class="p-8 text-center text-slate-400">
                                    No se encontraron registros de auditoría en el sistema.
                                </td>
                            </tr>
                            <tr 
                                v-for="log in logs.data" 
                                :key="log.id"
                                class="hover:bg-slate-50 transition-colors"
                            >
                                <td class="p-4 whitespace-nowrap text-slate-500">
                                    {{ formatDate(log.created_at) }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-red-100 border border-slate-300 rounded-full flex items-center justify-center font-black text-[10px] text-red-700 uppercase">
                                            {{ log.user ? log.user.name.substring(0,2).toUpperCase() : 'SYS' }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 text-xs leading-tight">
                                                {{ log.user ? log.user.name : 'Sistema Automático' }}
                                            </p>
                                            <p class="text-[9px] uppercase tracking-widest text-slate-400 font-black mt-0.5">
                                                {{ log.user ? log.user.role : 'System' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-900 font-bold leading-normal max-w-md">
                                    {{ log.action }}
                                </td>
                                <td class="p-4 whitespace-nowrap font-mono text-[11px] text-slate-400">
                                    {{ log.ip_address || '127.0.0.1' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PAGINADOR SIMPLE Y ROBUSTO -->
            <div class="flex justify-between items-center mt-6">
                <p class="text-xs font-black uppercase text-slate-400">
                    Mostrando {{ logs.from || 0 }} - {{ logs.to || 0 }} de {{ logs.total }} logs
                </p>
                <div class="flex gap-2">
                    <Link
                        v-if="logs.prev_page_url"
                        :href="logs.prev_page_url"
                        class="bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-900 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                    >
                        Anterior
                    </Link>
                    <Link
                        v-if="logs.next_page_url"
                        :href="logs.next_page_url"
                        class="bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-900 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                    >
                        Siguiente
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
