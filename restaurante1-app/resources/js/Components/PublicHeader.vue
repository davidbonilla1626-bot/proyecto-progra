<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';

// Obtenemos la cantidad de items del carrito para mostrar en la burbuja roja
const { cartCount } = useCart();
const page = usePage();
</script>

<template>
    <!-- BARRA DE NAVEGACIÓN SUPERIOR -->
    <nav class="sticky top-0 z-50 bg-white border-b-4 border-slate-900 px-6 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- LOGO -->
            <Link :href="route('public.menu')" class="text-2xl md:text-3xl font-black text-red-700 italic tracking-tighter uppercase font-['Epilogue'] hover:scale-105 transition-transform origin-left">
                QuickBite Express
            </Link>

            <!-- ENLACES CENTRALES -->
            <div class="hidden md:flex items-center gap-8 font-black text-slate-800 text-[14px] tracking-widest uppercase font-['Epilogue']">
                <Link 
                    :href="route('public.menu')" 
                    :class="[
                        'pb-1 transition-all border-b-2',
                        route().current('public.menu') 
                            ? 'text-red-700 border-red-700' 
                            : 'border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Menú
                </Link>
                <Link 
                    v-if="$page.props.auth && $page.props.auth.user"
                    :href="route('public.orders')" 
                    :class="[
                        'pb-1 transition-all border-b-2',
                        route().current('public.orders') 
                            ? 'text-red-700 border-red-700' 
                            : 'border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Mis Pedidos
                </Link>
                <Link 
                    v-if="$page.props.auth && $page.props.auth.user && $page.props.auth.user.role === 'user'"
                    :href="route('rewards.index')" 
                    :class="[
                        'pb-1 transition-all border-b-2',
                        route().current('rewards.index') 
                            ? 'text-red-700 border-red-700' 
                            : 'border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Recompensas
                </Link>
                <Link 
                    v-if="$page.props.auth && $page.props.auth.user"
                    :href="route('chat.index')" 
                    :class="[
                        'pb-1 transition-all border-b-2',
                        route().current('chat.index') 
                            ? 'text-red-700 border-red-700' 
                            : 'border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Soporte
                </Link>
                <Link 
                    :href="route('public.location')" 
                    :class="[
                        'pb-1 transition-all border-b-2',
                        route().current('public.location') 
                            ? 'text-red-700 border-red-700' 
                            : 'border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Ubicación
                </Link>
                <Link 
                    :href="route('public.about')" 
                    :class="[
                        'pb-1 transition-all border-b-2',
                        route().current('public.about') 
                            ? 'text-red-700 border-red-700' 
                            : 'border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Nosotros
                </Link>
            </div>

            <!-- BOTONES DERECHOS -->
            <div class="flex items-center gap-6">
                
                <!-- ICONO DEL CARRITO -->
                <Link :href="route('public.cart')" class="relative flex items-center p-2 text-slate-600 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-3xl font-bold">shopping_cart</span>
                    <span 
                        v-if="cartCount > 0" 
                        class="absolute top-0 -right-1 bg-red-700 text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-slate-900"
                    >
                        {{ cartCount }}
                    </span>
                </Link>

                <!-- AUTENTICACIÓN / ACCESOS -->
                <div class="flex items-center gap-3">
                    <div v-if="$page.props.auth && $page.props.auth.user" class="flex items-center gap-3">
                        <!-- PUNTOS BADGE -->
                        <Link 
                            v-if="$page.props.auth.user.role === 'user'" 
                            :href="route('rewards.index')" 
                            class="flex items-center gap-1 bg-amber-400 text-slate-950 border-2 border-slate-900 px-2.5 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                        >
                            <span class="material-symbols-outlined text-sm font-bold">star</span>
                            {{ $page.props.auth.user.points || 0 }} PTS
                        </Link>

                        <span class="hidden lg:inline text-xs font-black uppercase text-slate-400">
                            ¡HOLA, {{ $page.props.auth.user.name.split(' ')[0] }}!
                        </span>
                        
                        <!-- Panel Admin si es administrador -->
                        <Link 
                            v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.email.includes('david')" 
                            :href="route('dashboard')" 
                            class="bg-[#ffcc00] text-slate-950 border-2 border-slate-900 px-4 py-2 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                        >
                            PANEL ADMIN
                        </Link>

                        <!-- Panel Cocina si es empleado o admin -->
                        <Link 
                            v-if="$page.props.auth.user.role === 'employee' || $page.props.auth.user.role === 'admin'" 
                            :href="route('kitchen.index')" 
                            class="bg-blue-500 text-white border-2 border-slate-900 px-4 py-2 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                        >
                            PANEL COCINA
                        </Link>
                        
                        <!-- Botón de Cerrar Sesión -->
                        <Link 
                            :href="route('logout')" 
                            method="post" 
                            as="button" 
                            class="bg-slate-900 hover:bg-slate-800 text-white border-2 border-slate-900 px-4 py-2 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer"
                        >
                            SALIR
                        </Link>
                    </div>
                    
                    <Link 
                        v-else 
                        :href="route('login')" 
                        class="bg-red-700 text-white px-5 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest border-2 border-slate-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                    >
                        INICIAR SESIÓN
                    </Link>
                </div>
            </div>
        </div>
    </nav>

    <!-- BANNER DE NOTIFICACIONES GLOBALES -->
    <div class="fixed top-24 right-6 z-50 flex flex-col gap-3 max-w-sm w-full">
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
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
