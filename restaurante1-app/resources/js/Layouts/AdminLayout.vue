<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex font-['Be_Vietnam_Pro'] text-slate-900">
        
        <!-- SIDEBAR DE NAVEGACIÓN (Panel Izquierdo) -->
        <aside class="w-64 bg-white border-r-2 border-slate-200 flex flex-col shrink-0 h-screen sticky top-0">
            <!-- Logo -->
            <div class="p-6 border-b-2 border-slate-200">
                <Link :href="route('dashboard')" class="block hover:scale-105 transition-transform origin-left">
                    <h1 class="text-xl font-black italic tracking-tighter text-red-700 font-['Epilogue'] uppercase leading-none">
                        QuickBite Express
                    </h1>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mt-1">Admin Panel HQ</p>
                </Link>
            </div>

            <!-- Perfil del Usuario -->
            <div class="p-6">
                <div class="border-2 border-slate-900 rounded-2xl p-3 flex items-center gap-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-white relative overflow-hidden group cursor-pointer hover:translate-x-1 transition-transform">
                    <div class="w-10 h-10 rounded-full bg-red-700 flex items-center justify-center border-2 border-slate-900 shrink-0 relative z-10 text-white font-black text-sm">
                        <!-- Iniciales hardcoded para el ejemplo de la imagen o dinámico -->
                        DB
                    </div>
                    <div class="relative z-10">
                        <p class="font-black text-[15px] leading-tight text-slate-900 truncate max-w-[120px]">{{ $page.props.auth?.user?.name || 'David Bonilla' }}</p>
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 mt-0.5">HEAD CHEF</p>
                    </div>
                    <!-- Hover Effect Background -->
                    <div class="absolute inset-0 bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            </div>

            <!-- Enlaces del Menú -->
            <nav class="flex-1 px-4 space-y-3 mt-2 overflow-y-auto">
                <Link 
                    :href="route('dashboard')" 
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl border-2 border-slate-900 bg-yellow-400 font-black text-[16px] text-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-transform translate-x-1"
                >
                    <span class="material-symbols-outlined text-[24px]">dashboard</span>
                    Dashboard
                </Link>
                
                <a href="#" class="w-full flex items-center gap-4 px-4 py-3 rounded-2xl border-2 border-transparent font-black text-[16px] text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                    Live Orders
                </a>
                
                <a href="#" class="w-full flex items-center gap-4 px-4 py-3 rounded-2xl border-2 border-transparent font-black text-[16px] text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                    Inventory
                </a>

                <a href="#" class="w-full flex items-center gap-4 px-4 py-3 rounded-2xl border-2 border-transparent font-black text-[16px] text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-[24px]">monitoring</span>
                    Sales Reports
                </a>

                <a href="#" class="w-full flex items-center gap-4 px-4 py-3 rounded-2xl border-2 border-transparent font-black text-[16px] text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-[24px]">settings</span>
                    Settings
                </a>
            </nav>

            <!-- Acciones al fondo -->
            <div class="p-6 mt-auto border-t border-slate-200">
                <button class="w-full bg-red-700 text-white font-black uppercase text-[13px] tracking-widest py-4 px-4 rounded-xl border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all text-center">
                    ADD NEW PRODUCT
                </button>

                <!-- Botón de Cerrar Sesión (Importante para salir del admin) -->
                <div class="mt-6 text-center">
                     <Link :href="route('logout')" method="post" as="button" class="text-[12px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors flex items-center justify-center gap-2 mx-auto">
                        <span class="material-symbols-outlined text-[18px]">logout</span> LOG OUT
                    </Link>
                </div>
            </div>
        </aside>

        <!-- ÁREA PRINCIPAL (Donde se carga el contenido dinámico) -->
        <main class="flex-1 h-screen overflow-y-auto">
            <slot />
        </main>

    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
