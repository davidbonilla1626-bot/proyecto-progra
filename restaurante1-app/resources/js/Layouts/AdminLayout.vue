<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

// Obtener iniciales del usuario logueado
const userInitials = computed(() => {
    if (!user.value || !user.value.name) return 'QB';
    const parts = user.value.name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return user.value.name.substring(0, 2).toUpperCase();
});

// Clases activas para los enlaces
const getLinkClass = (routeName) => {
    const isActive = route().current(routeName);
    return [
        'w-full flex items-center gap-4 px-4 py-3 rounded-2xl border-2 border-slate-900 font-black text-[15px] transition-all',
        isActive 
            ? 'bg-[#ffcc00] text-slate-950 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] translate-x-1' 
            : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 hover:text-slate-950'
    ];
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex font-['Be_Vietnam_Pro'] text-slate-900">
        
        <!-- SIDEBAR DE NAVEGACIÓN (Panel Izquierdo) -->
        <aside class="w-64 bg-white border-r-2 border-slate-200 flex flex-col shrink-0 h-screen sticky top-0 z-20">
            <!-- Logo -->
            <div class="p-6 border-b-2 border-slate-200">
                <Link :href="route('dashboard')" class="block hover:scale-105 transition-transform origin-left">
                    <h1 class="text-xl font-black italic tracking-tighter text-red-700 font-['Epilogue'] uppercase leading-none">
                        QuickBite Express
                    </h1>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mt-1">Panel Administrativo</p>
                </Link>
            </div>

            <!-- Perfil del Usuario -->
            <div class="p-6">
                <div class="border-2 border-slate-900 rounded-2xl p-3 flex items-center gap-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-white relative overflow-hidden group">
                    <div class="w-10 h-10 rounded-full bg-red-700 flex items-center justify-center border-2 border-slate-900 shrink-0 relative z-10 text-white font-black text-sm">
                        {{ userInitials }}
                    </div>
                    <div class="relative z-10">
                        <p class="font-black text-[13px] leading-tight text-slate-900 truncate max-w-[120px]" :title="user?.name">
                            {{ user?.name || 'Administrador' }}
                        </p>
                        <p class="text-[9px] font-black uppercase tracking-widest text-red-700 mt-0.5">ADMINISTRADOR</p>
                    </div>
                </div>
            </div>

            <!-- Enlaces del Menú -->
            <nav class="flex-grow px-4 space-y-2 mt-2 overflow-y-auto">
                <Link 
                    :href="route('dashboard')" 
                    :class="getLinkClass('dashboard')"
                >
                    <span class="material-symbols-outlined text-[22px]">dashboard</span>
                    Dashboard
                </Link>
                
                <Link 
                    :href="route('public.orders')" 
                    :class="getLinkClass('public.orders')"
                >
                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                    Despacho / Pedidos
                </Link>
                
                <Link 
                    :href="route('products.index')" 
                    :class="getLinkClass('products.index')"
                >
                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                    Inventario Platos
                </Link>

                <Link 
                    :href="route('categories.index')" 
                    :class="getLinkClass('categories.index')"
                >
                    <span class="material-symbols-outlined text-[22px]">category</span>
                    Categorías Menú
                </Link>
            </nav>

            <!-- Acciones al fondo -->
            <div class="p-6 mt-auto border-t border-slate-200">
                <Link 
                    :href="route('products.create')"
                    class="w-full bg-red-700 hover:bg-red-800 text-white font-black uppercase text-[11px] tracking-widest py-3.5 px-4 rounded-xl border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all text-center block"
                >
                    NUEVO PRODUCTO
                </Link>

                <!-- Botón de Cerrar Sesión -->
                <div class="mt-6 text-center">
                     <Link :href="route('logout')" method="post" as="button" class="text-[11px] font-black uppercase tracking-widest text-slate-400 hover:text-red-700 transition-colors flex items-center justify-center gap-2 mx-auto cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] font-bold">logout</span> CERRAR SESIÓN
                    </Link>
                </div>
            </div>
        </aside>

        <!-- ÁREA PRINCIPAL -->
        <main class="flex-1 h-screen overflow-y-auto">
            <slot />
        </main>

        <!-- BANNER DE NOTIFICACIONES GLOBALES -->
        <div class="fixed top-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full">
            <TransitionGroup name="list">
                <div v-if="page.props.flash?.message" key="msg" class="bg-emerald-100 border-4 border-slate-900 p-4 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-emerald-950 flex gap-3 items-start animate-scaleUp">
                    <span class="material-symbols-outlined text-emerald-700 font-bold shrink-0">check_circle</span>
                    <div class="flex-grow">
                        <p class="text-xs font-black uppercase tracking-wider text-emerald-800">Éxito</p>
                        <p class="text-xs font-bold mt-1 leading-normal text-slate-800">{{ page.props.flash.message }}</p>
                    </div>
                </div>
                <div v-if="page.props.flash?.error" key="err" class="bg-red-100 border-4 border-slate-900 p-4 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-red-950 flex gap-3 items-start animate-scaleUp">
                    <span class="material-symbols-outlined text-red-700 font-bold shrink-0">error</span>
                    <div class="flex-grow">
                        <p class="text-xs font-black uppercase tracking-wider text-red-800">Error</p>
                        <p class="text-xs font-bold mt-1 leading-normal text-slate-800">{{ page.props.flash.error }}</p>
                    </div>
                </div>
                <div v-if="page.props.errors && Object.keys(page.props.errors).length > 0" key="validation-err" class="bg-yellow-100 border-4 border-slate-900 p-4 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-yellow-950 flex gap-3 items-start animate-scaleUp">
                    <span class="material-symbols-outlined text-yellow-600 font-bold shrink-0">warning</span>
                    <div class="flex-grow">
                        <p class="text-xs font-black uppercase tracking-wider text-yellow-850">Atención / Validación</p>
                        <ul class="text-[11px] font-bold mt-1 list-disc pl-4 space-y-1 text-slate-800">
                            <li v-for="(err, key) in page.props.errors" :key="key">{{ err }}</li>
                        </ul>
                    </div>
                </div>
            </TransitionGroup>
        </div>

    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
