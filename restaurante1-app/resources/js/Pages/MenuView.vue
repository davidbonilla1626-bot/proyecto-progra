<script setup>
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

const selectedCategory = ref('Todo');
const searchQuery = ref('');

// Definimos las categorías con iconos
const categories = [
    { name: 'Todo', icon: 'restaurant_menu' },
    { name: 'Hamburguesas', icon: 'lunch_dining' },
    { name: 'Hot Dogs', icon: 'hot_tub' }, 
    { name: 'Pollo', icon: 'kebab_dining' },
    { name: 'Acompañamientos', icon: 'tapas' },
    { name: 'Bebidas', icon: 'local_drink' },
    { name: 'Ensaladas', icon: 'eco' }
];

const filteredProducts = computed(() => {
    let result = props.products;
    
    // Filtrar por categoría seleccionada
    if (selectedCategory.value !== 'Todo') {
        result = result.filter(p => {
            const categoryName = typeof p.category === 'object' ? p.category?.name : p.category;
            return categoryName === selectedCategory.value;
        });
    }

    // Filtrar por búsqueda dinámica (Nombre, Categoría, Descripción)
    if (searchQuery.value.trim() !== '') {
        const query = searchQuery.value.toLowerCase().trim();
        result = result.filter(p => {
            const categoryName = (typeof p.category === 'object' ? p.category?.name : p.category) || '';
            return p.name.toLowerCase().includes(query) ||
                   p.description.toLowerCase().includes(query) ||
                   categoryName.toLowerCase().includes(query);
        });
    }

    return result;
});

// Formateador de moneda profesional
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

// --- ALGORITMO DE RECOMENDACIÓN IA SENCILLA ---
const showAiRecommendation = ref(false);
const currentProductForAi = ref(null);
const recommendedProductForAi = ref(null);
const aiMessage = ref('');

const handleAddToCartWithAi = (product) => {
    // 1. Agregar el producto principal al carrito
    addToCart(product);
    currentProductForAi.value = product;

    // 2. Algoritmo de emparejamiento inteligente de categorías
    const allProducts = props.products;
    const currentCat = (typeof product.category === 'object' ? product.category?.name : product.category) || 'Hamburguesas';

    let targetCategory = 'Bebidas';
    if (currentCat === 'Bebidas') {
        targetCategory = 'Hamburguesas';
    } else if (currentCat === 'Acompañamientos') {
        targetCategory = 'Bebidas';
    } else {
        targetCategory = 'Acompañamientos'; // hamburguesas o alitas sugieren papas/aros
    }

    // Filtrar candidatos disponibles con stock
    let candidates = allProducts.filter(p => {
        const pCat = (typeof p.category === 'object' ? p.category?.name : p.category) || '';
        return pCat === targetCategory && p.id !== product.id && p.stock > 0;
    });

    // Fallback si no hay existencias en la categoría objetivo
    if (candidates.length === 0) {
        candidates = allProducts.filter(p => p.id !== product.id && p.stock > 0);
    }

    if (candidates.length > 0) {
        // Elegir una recomendación aleatoria de los candidatos
        const recommended = candidates[Math.floor(Math.random() * candidates.length)];
        recommendedProductForAi.value = recommended;

        // Mensajes ingeniosos de nuestro bot de IA
        const phrases = [
            `🤖 [BITE-BOT IA]: ¡Gran elección! Mi algoritmo predice que acompañar tu "${product.name}" con unas deliciosas "${recommended.name}" aumentará tu nivel de felicidad culinaria en un 97.2%.`,
            `🤖 [SABOR E INTELIGENCIA]: Has añadido "${product.name}". Para lograr el equilibrio perfecto de sabores y carbohidratos, te sugiero complementarlo con "${recommended.name}".`,
            `🤖 [RECOMENDACIÓN INSTANTÁNEA]: Detectamos que tienes hambre de "${product.name}". Te falta el combo perfecto: añade "${recommended.name}" por solo ${formatPrice(recommended.price)}.`
        ];
        
        aiMessage.value = phrases[Math.floor(Math.random() * phrases.length)];
        showAiRecommendation.value = true;
    }
};

const addAiRecommendationToCart = () => {
    if (recommendedProductForAi.value) {
        addToCart(recommendedProductForAi.value);
    }
    showAiRecommendation.value = false;
};
</script>

<template>
    <Head title="El Menú | QuickBite Express" />
    
    <div class="bg-[#f8f9fa] text-[#191c1d] min-h-screen font-['Be_Vietnam_Pro'] pb-24">
        
        <!-- HEADER / NAVEGACIÓN -->
        <PublicHeader />

        <div class="flex max-w-7xl mx-auto">
            
            <!-- SIDEBAR: Filtros de categorías -->
            <aside class="hidden md:block w-72 p-6 border-r-2 border-slate-200 sticky top-20 h-[calc(100vh-80px)] overflow-y-auto bg-white">
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight font-['Epilogue']">CATEGORÍAS</h2>
                    <p class="text-xs text-slate-400 font-bold uppercase mt-1">QuickBite Express HQ</p>
                </div>

                <nav class="space-y-3">
                    <button 
                        v-for="cat in categories" :key="cat.name"
                        @click="selectedCategory = cat.name"
                        :class="[
                            'w-full flex items-center gap-4 text-left p-4 rounded-2xl font-black transition-all text-[15px]',
                            selectedCategory === cat.name 
                                ? 'bg-yellow-400 border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] translate-x-1 text-slate-900' 
                                : 'bg-transparent border-2 border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <span class="material-symbols-outlined text-[24px] font-bold">{{ cat.icon }}</span>
                        {{ cat.name }}
                    </button>
                </nav>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="flex-1 p-6 md:p-10 bg-slate-50">
                <header class="mb-10 border-b-2 border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <h1 class="text-4xl md:text-5xl font-black italic uppercase font-['Epilogue'] tracking-tighter leading-none text-slate-900">
                        SABOR A <span class="text-red-700">ALTA VELOCIDAD</span>
                    </h1>

                    <!-- Buscador dinámico -->
                    <div class="relative w-full md:w-80 shrink-0">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined font-bold">search</span>
                        </span>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Buscar por nombre, ingrediente o cat..." 
                            class="pl-10 pr-4 py-2.5 w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-all text-xs font-bold bg-white text-slate-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]"
                        />
                    </div>
                </header>

                <!-- GRID DE PRODUCTOS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="(product, index) in filteredProducts" :key="product.id" 
                        class="bg-white border-2 border-slate-900 rounded-3xl overflow-hidden group hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all duration-300 flex flex-col">
                        
                        <!-- Imagen -->
                        <div class="relative h-56 bg-slate-100 overflow-hidden border-b-2 border-slate-900">
                            <img :src="product.image || product.image_path" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Alerta de stock o Más vendido -->
                            <div v-if="product.stock === 0" class="absolute top-4 right-4 bg-red-700 border-2 border-slate-900 px-3 py-1.5 text-[9px] font-black uppercase text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] z-10">
                                AGOTADO
                            </div>
                            <div v-else-if="product.stock <= 5" class="absolute top-4 right-4 bg-yellow-400 border-2 border-slate-900 px-3 py-1.5 text-[9px] font-black uppercase text-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-1 z-10">
                                <span class="material-symbols-outlined text-[12px] font-bold">warning</span>
                                ¡POCO STOCK! ({{ product.stock }})
                            </div>
                            <div v-else-if="index < 2 && selectedCategory === 'Todo'" class="absolute top-4 left-4 bg-yellow-400 border-2 border-slate-900 px-3 py-1.5 text-[9px] font-black uppercase text-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                MÁS VENDIDO
                            </div>
                        </div>

                        <!-- Información -->
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="font-black text-lg mb-2 font-['Epilogue'] tracking-tight leading-tight text-slate-900">{{ product.name }}</h3>
                            <p class="text-slate-500 text-xs leading-relaxed mb-6 flex-1 font-bold">{{ product.description }}</p>
                            
                            <div class="flex items-center justify-between mt-auto">
                                <span class="text-xl font-black text-red-700 font-['Epilogue']">{{ formatPrice(product.price) }}</span>
                                
                                <!-- Botón Agregar al Carrito -->
                                <button 
                                    @click="handleAddToCartWithAi(product)"
                                    :disabled="product.stock <= 0"
                                    :class="[
                                        'px-4 py-2.5 rounded-xl border-2 border-slate-900 font-black text-xs uppercase transition-all flex items-center gap-1.5 cursor-pointer shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]',
                                        product.stock <= 0 
                                            ? 'bg-slate-350 text-slate-500 border-slate-400 cursor-not-allowed shadow-none' 
                                            : 'bg-yellow-400 hover:bg-yellow-500 text-slate-900 hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px]'
                                    ]"
                                >
                                    <span class="material-symbols-outlined text-sm font-bold">{{ product.stock <= 0 ? 'block' : 'add' }}</span>
                                    {{ product.stock <= 0 ? 'AGOTADO' : 'AÑADIR' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado vacío -->
                <div v-if="filteredProducts.length === 0" class="text-center py-20">
                    <span class="material-symbols-outlined text-5xl text-slate-300 mb-2">sentiment_dissatisfied</span>
                    <p class="text-slate-400 text-lg italic font-bold">No se encontraron productos en esta categoría.</p>
                </div>
            </main>
        </div>

        <!-- BANNER FLOTANTE DEL CARRITO -->
        <div v-if="cartCount > 0" class="fixed bottom-6 left-0 w-full z-50 px-4 flex justify-center pointer-events-none">
            <Link :href="route('public.cart')" class="pointer-events-auto w-full max-w-4xl bg-red-700 text-white border-4 border-slate-900 rounded-3xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-1 hover:translate-y-1 transition-all flex items-center justify-between p-4 px-6 group cursor-pointer overflow-hidden relative">
                
                <div class="flex items-center gap-4 relative z-10">
                    <div class="bg-yellow-400 text-slate-900 p-3 rounded-xl border-2 border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <span class="material-symbols-outlined font-bold">shopping_bag</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-red-200">VER TU BOLSA</p>
                        <p class="text-xl font-black font-['Epilogue'] uppercase">{{ cartCount }} Productos listos</p>
                    </div>
                </div>

                <div class="text-right relative z-10">
                    <p class="text-2xl font-black font-['Epilogue']">{{ formatPrice(cartSubtotal) }}</p>
                    <p class="text-[10px] font-black uppercase tracking-widest text-yellow-400 group-hover:text-white transition-colors">COMPLETAR PEDIDO</p>
                </div>

                <!-- Fondo decorativo al hacer hover -->
                <div class="absolute inset-0 bg-red-800 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </Link>
        </div>

        <!-- MODAL DE RECOMENDACIÓN CON IA -->
        <div v-if="showAiRecommendation && recommendedProductForAi" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm pointer-events-auto">
            <div class="bg-white border-4 border-slate-900 rounded-3xl p-6 max-w-md w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] animate-scaleUp space-y-6">
                <!-- CABECERA DEL BOT -->
                <div class="bg-[#ffcc00] border-2 border-slate-900 rounded-xl p-3 flex items-center gap-3">
                    <span class="material-symbols-outlined text-[24px] text-slate-950 font-bold">smart_toy</span>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-700">QuickBite IA System</p>
                        <p class="text-[12px] font-black text-slate-950">Asistente de Combinación</p>
                    </div>
                </div>

                <!-- MENSAJE DE IA -->
                <p class="text-xs text-slate-700 font-bold leading-relaxed bg-slate-50 border-2 border-slate-200 p-3.5 rounded-xl">
                    {{ aiMessage }}
                </p>

                <!-- CARD DE PRODUCTO RECOMENDADO -->
                <div class="border-2 border-slate-900 rounded-2xl p-3 flex gap-4 bg-slate-50/50">
                    <img 
                        :src="recommendedProductForAi.image || recommendedProductForAi.image_path" 
                        :alt="recommendedProductForAi.name" 
                        class="w-16 h-16 object-cover rounded-xl border-2 border-slate-900 shrink-0" 
                    />
                    <div class="min-w-0 flex-grow">
                        <p class="font-black text-slate-900 text-xs truncate">{{ recommendedProductForAi.name }}</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase mt-0.5">Complemento Sugerido</p>
                        <p class="text-red-700 font-black text-xs mt-1.5 font-['Epilogue']">
                            {{ formatPrice(recommendedProductForAi.price) }}
                        </p>
                    </div>
                </div>

                <!-- BOTONES DE ACCION -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        @click="addAiRecommendationToCart"
                        class="bg-[#ffcc00] hover:bg-yellow-500 text-slate-950 font-black border-2 border-slate-900 rounded-xl px-4 py-3 text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all w-full text-center cursor-pointer"
                    >
                        Añadir Combo 🚀
                    </button>
                    <button 
                        @click="showAiRecommendation = false"
                        class="bg-white hover:bg-slate-50 text-slate-500 font-bold border-2 border-slate-200 rounded-xl px-4 py-3 text-xs uppercase transition-all w-full text-center cursor-pointer"
                    >
                        No, gracias
                    </button>
                </div>
            </div>
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