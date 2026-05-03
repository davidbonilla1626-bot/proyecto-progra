<script setup>
/**
 * IMPORTACIONES
 */
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import { useCart } from '@/Composables/useCart';

// RECIBIENDO LOS DATOS desde web.php
const props = defineProps({
    products: Array
});

// Extraemos las funciones del carrito global
const { addToCart, cartCount, cartSubtotal } = useCart();

/**
 * LÓGICA DE FILTRADO
 */
const selectedCategory = ref('Todo');

// Definimos las categorías con iconos
const categories = [
    { name: 'Todo', icon: 'restaurant_menu' },
    { name: 'Hamburguesas', icon: 'lunch_dining' },
    { name: 'Hot Dogs', icon: 'hot_tub' }, // Material icons approximation
    { name: 'Pollo', icon: 'kebab_dining' },
    { name: 'Acompañamientos', icon: 'tapas' },
    { name: 'Bebidas', icon: 'local_drink' },
    { name: 'Ensaladas', icon: 'eco' }
];

/**
 * COMPUTED PROPERTY
 * Filtra los productos basándose en la categoría seleccionada.
 * Verificamos p.category?.name porque ahora category es un objeto relacionado de la base de datos.
 */
const filteredProducts = computed(() => {
    if (selectedCategory.value === 'Todo') return props.products;
    return props.products.filter(p => {
        // Soporta tanto objetos (base de datos real) como strings (datos de prueba antiguos)
        const categoryName = typeof p.category === 'object' ? p.category?.name : p.category;
        return categoryName === selectedCategory.value;
    });
});

// Formateador de moneda profesional
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};
</script>

<template>
    <Head title="QuickBite Express | Menú" />
    
    <div class="bg-[#f8f9fa] text-[#191c1d] min-h-screen font-['Be_Vietnam_Pro'] pb-24">
        
        <!-- HEADER / NAVEGACIÓN -->
        <PublicHeader />

        <div class="flex max-w-7xl mx-auto">
            
            <!-- SIDEBAR: Filtros de categorías -->
            <aside class="hidden md:block w-72 p-6 border-r-2 border-slate-200 sticky top-20 h-[calc(100vh-80px)] overflow-y-auto bg-white">
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight font-['Epilogue']">Menu Categories</h2>
                    <p class="text-sm text-slate-500 mt-1">QuickBite Express HQ</p>
                </div>

                <nav class="space-y-3">
                    <button 
                        v-for="cat in categories" :key="cat.name"
                        @click="selectedCategory = cat.name"
                        :class="[
                            'w-full flex items-center gap-4 text-left p-4 rounded-2xl font-black transition-all text-[16px]',
                            selectedCategory === cat.name 
                                ? 'bg-yellow-400 border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] translate-x-1 text-slate-900' 
                                : 'bg-transparent border-2 border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <span class="material-symbols-outlined text-[24px]">{{ cat.icon }}</span>
                        {{ cat.name }}
                    </button>
                </nav>
            </aside>

            <!-- CONTENIDO PRINCIPAL: Grid de productos -->
            <main class="flex-1 p-6 md:p-10 bg-slate-50">
                <header class="mb-10">
                    <h1 class="text-5xl font-black italic uppercase font-['Epilogue'] tracking-tighter leading-none text-slate-900">
                        HIGH-VELOCITY FLAVOR
                    </h1>
                </header>

                <!-- GRID DE PRODUCTOS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Usamos los productos filtrados aquí -->
                    <div v-for="(product, index) in filteredProducts" :key="product.id" 
                        class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden group hover:shadow-[8px_8px_0px_0px_rgba(25,28,29,1)] transition-all duration-300 flex flex-col">
                        
                        <!-- Imagen -->
                        <div class="relative h-56 bg-slate-100 overflow-hidden border-b-2 border-slate-900">
                            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Etiqueta BEST SELLER (Solo a los primeros 2 para simular) -->
                            <div v-if="index < 2 && selectedCategory === 'Todo'" class="absolute top-4 left-4 bg-yellow-400 border-2 border-slate-900 px-3 py-1 text-[10px] font-black uppercase text-slate-900">
                                BEST SELLER
                            </div>
                        </div>

                        <!-- Información -->
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="font-bold text-xl mb-2 font-['Epilogue'] tracking-tight leading-tight text-slate-900">{{ product.name }}</h3>
                            <p class="text-slate-500 text-xs leading-relaxed mb-6 flex-1">{{ product.description }}</p>
                            
                            <div class="flex items-center justify-between mt-auto">
                                <span class="text-xl font-black text-red-700 font-['Epilogue']">{{ formatPrice(product.price) }}</span>
                                
                                <!-- Botón Agregar al Carrito -->
                                <button 
                                    @click="addToCart(product)"
                                    class="bg-yellow-400 text-slate-900 px-4 py-2 rounded-lg border-2 border-slate-900 font-bold text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all flex items-center gap-1"
                                >
                                    <span class="material-symbols-outlined text-sm font-bold">add</span> ADD
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado vacío: Por si no hay productos en una categoría -->
                <div v-if="filteredProducts.length === 0" class="text-center py-20">
                    <p class="text-slate-400 text-xl italic font-semibold">No se encontraron productos en esta categoría.</p>
                </div>
            </main>
        </div>

        <!-- STICKY BOTTOM CART BANNER -->
        <!-- Solo se muestra si hay items en el carrito -->
        <div v-if="cartCount > 0" class="fixed bottom-6 left-0 w-full z-50 px-4 flex justify-center pointer-events-none">
            <Link :href="route('public.cart')" class="pointer-events-auto w-full max-w-4xl bg-red-700 text-white border-4 border-slate-900 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-1 hover:translate-y-1 transition-all flex items-center justify-between p-4 px-6 group cursor-pointer overflow-hidden relative">
                
                <div class="flex items-center gap-4 relative z-10">
                    <!-- Icono Bolsa -->
                    <div class="bg-yellow-400 text-slate-900 p-3 rounded-xl border-2 border-slate-900">
                        <span class="material-symbols-outlined">shopping_bag</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-red-200">View Your Bag</p>
                        <p class="text-xl font-black font-['Epilogue'] uppercase">{{ cartCount }} Items Ready</p>
                    </div>
                </div>

                <div class="text-right relative z-10">
                    <p class="text-2xl font-black font-['Epilogue']">{{ formatPrice(cartSubtotal) }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-yellow-400 group-hover:text-white transition-colors">Fast Checkout</p>
                </div>

                <!-- Fondo decorativo al hacer hover -->
                <div class="absolute inset-0 bg-red-800 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </Link>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');

aside::-webkit-scrollbar {
    width: 0;
}
</style>