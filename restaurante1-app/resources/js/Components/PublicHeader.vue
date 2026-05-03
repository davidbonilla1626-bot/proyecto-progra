<script setup>
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';

// Obtenemos la cantidad de items del carrito para mostrar en la burbuja roja
const { cartCount } = useCart();
</script>

<template>
    <!-- BARRA DE NAVEGACIÓN SUPERIOR (Reutilizable para el Menú y Checkout) -->
    <nav class="sticky top-0 z-50 bg-white border-b-4 border-slate-900 px-6 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- LOGO: Al hacer clic lleva al Menú -->
            <Link :href="route('public.menu')" class="text-2xl md:text-3xl font-black text-red-700 italic tracking-tighter uppercase font-['Epilogue'] hover:scale-105 transition-transform origin-left">
                QuickBite Express
            </Link>

            <!-- ENLACES CENTRALES (Ocultos en móviles, visibles en pantallas grandes) -->
            <div class="hidden md:flex items-center gap-8 font-black text-slate-800 text-[16px] tracking-wide font-['Epilogue']">
                <!-- El enlace de menú se pone en rojo si estamos en la vista de menú -->
                <Link 
                    :href="route('public.menu')" 
                    :class="[
                        'pb-1 transition-all',
                        route().current('public.menu') 
                            ? 'text-red-700 border-b-2 border-red-700' 
                            : 'border-b-2 border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Menu
                </Link>
                <Link 
                    :href="route('public.orders')" 
                    :class="[
                        'pb-1 transition-all',
                        route().current('public.orders') 
                            ? 'text-red-700 border-b-2 border-red-700' 
                            : 'border-b-2 border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    Orders
                </Link>
                <Link 
                    :href="route('public.about')" 
                    :class="[
                        'pb-1 transition-all',
                        route().current('public.about') 
                            ? 'text-red-700 border-b-2 border-red-700' 
                            : 'border-b-2 border-transparent hover:text-red-700 hover:border-slate-200'
                    ]"
                >
                    About
                </Link>
            </div>

            <!-- BOTONES DERECHOS (Carrito y Login) -->
            <div class="flex items-center gap-6">
                
                <!-- ICONO DEL CARRITO -->
                <Link :href="route('public.cart') || '#'" class="relative flex items-center p-2 text-slate-600 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-3xl">shopping_cart</span>
                    <!-- Notificación roja (Badge) con la cantidad de productos -->
                    <span 
                        v-if="cartCount > 0" 
                        class="absolute top-0 -right-1 bg-red-700 text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border border-white"
                    >
                        {{ cartCount }}
                    </span>
                </Link>

                <!-- BOTÓN DE LOGIN / DASHBOARD -->
                <Link 
                    v-if="$page.props.auth && $page.props.auth.user" 
                    :href="route('dashboard')" 
                    class="bg-red-700 text-white px-6 py-2.5 rounded-2xl font-black uppercase text-sm tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all"
                >
                    Dashboard
                </Link>
                <Link 
                    v-else 
                    :href="route('login')" 
                    class="bg-red-700 text-white px-6 py-2.5 rounded-2xl font-black uppercase text-sm tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all"
                >
                    Login
                </Link>
            </div>
        </div>
    </nav>
</template>
