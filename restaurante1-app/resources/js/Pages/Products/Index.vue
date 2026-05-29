<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'; 
import { Head, Link, router } from '@inertiajs/vue3'; 
import { ref, computed } from 'vue';

const props = defineProps({
    products: Array,
    categories: Array
});

// Búsqueda en tiempo real
const searchQuery = ref('');

const filteredProducts = computed(() => {
    if (!searchQuery.value) return props.products;
    const q = searchQuery.value.toLowerCase();
    return props.products.filter(p => 
        p.name.toLowerCase().includes(q) || 
        (p.category?.name && p.category.name.toLowerCase().includes(q))
    );
});

// Formateador de moneda profesional
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

// Eliminar producto de la base de datos
const deleteProduct = (id, name) => {
    if (confirm(`¿Estás seguro de que deseas eliminar el producto "${name}" del menú? Esta acción no se puede deshacer.`)) {
        router.delete(route('products.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Gestión de Inventario | Panel Administrativo" />

    <AdminLayout>
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-10">
            
            <!-- HEADER DE LA PÁGINA -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-3xl font-black italic uppercase tracking-tighter text-red-700 font-['Epilogue'] flex items-center gap-2">
                        <span class="material-symbols-outlined text-3xl">inventory_2</span>
                        GESTIÓN DE INVENTARIO
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed font-bold">
                        Administra todos los platillos disponibles en el menú de QuickBite Express. 
                        Puedes agregar, editar o retirar productos del sistema central.
                    </p>
                </div>
                
                <!-- BOTÓN PRINCIPAL DE AGREGAR PRODUCTO -->
                <Link :href="route('products.create')" class="bg-red-700 text-white px-6 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest flex items-center gap-2 border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all shrink-0">
                    <span class="material-symbols-outlined text-[18px] font-bold">add_circle</span> 
                    AGREGAR PRODUCTO
                </Link>
            </div>

            <!-- CONTROLES DE BÚSQUEDA Y FILTRADO -->
            <div class="flex gap-4 max-w-md">
                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined font-bold">search</span>
                    </span>
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Buscar por nombre o categoría..." 
                        class="pl-10 pr-4 py-3 w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-all text-sm font-bold bg-white text-slate-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]"
                    />
                </div>
            </div>

            <!-- CONTENEDOR DE LA TABLA DE PRODUCTOS -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <!-- CABECERA -->
                        <thead class="text-[11px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-900 bg-slate-50">
                            <tr>
                                <th class="px-6 py-5">PRODUCTO</th>
                                <th class="px-6 py-5">CATEGORÍA</th>
                                <th class="px-6 py-5">PRECIO</th>
                                <th class="px-6 py-5 text-right font-bold">ACCIONES</th>
                            </tr>
                        </thead>
                        
                        <!-- CUERPO -->
                        <tbody class="divide-y divide-slate-200 font-bold">
                            
                            <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-yellow-50 transition-colors group">
                                
                                <!-- NOMBRE Y DETALLE -->
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg border-2 border-slate-900 overflow-hidden shrink-0">
                                        <img :src="product.image || product.image_path" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-black text-[15px] text-slate-900 leading-tight font-['Epilogue']">
                                            {{ product.name }}
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1 line-clamp-1 max-w-sm font-medium">
                                            {{ product.description }}
                                        </p>
                                    </div>
                                </td>
                                
                                <!-- CATEGORÍA -->
                                <td class="px-6 py-4">
                                    <span class="bg-[#ffcc00] text-slate-900 px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border-2 border-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                        {{ product.category?.name || 'Menú general' }}
                                    </span>
                                </td>
                                
                                <!-- PRECIO -->
                                <td class="px-6 py-4">
                                    <span class="font-black text-red-700 text-lg font-['Epilogue']">
                                        {{ formatPrice(product.price) }}
                                    </span>
                                </td>
                                
                                <!-- ACCIONES -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        
                                        <!-- Editar -->
                                        <Link :href="route('products.edit', product.id)" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-yellow-400 text-slate-900 flex items-center justify-center hover:bg-yellow-500 transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]" title="Editar Producto">
                                            <span class="material-symbols-outlined text-[16px] font-bold">edit</span>
                                        </Link>
                                        
                                        <!-- Eliminar -->
                                        <button @click="deleteProduct(product.id, product.name)" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-red-700 text-white flex items-center justify-center hover:bg-red-800 transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] cursor-pointer" title="Eliminar Producto">
                                            <span class="material-symbols-outlined text-[16px] font-bold">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- ESTADO VACÍO -->
                            <tr v-if="filteredProducts.length === 0">
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">inventory_2</span>
                                    <p class="text-slate-500 font-bold text-lg">El inventario está vacío.</p>
                                    <p class="text-slate-400 text-sm mt-1 font-medium">No se encontraron productos coincidentes.</p>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                
                <!-- FOOTER DE LA TABLA -->
                <div class="bg-slate-50 border-t-2 border-slate-900 p-4 text-xs font-black text-slate-500 text-center uppercase tracking-widest">
                    MOSTRANDO UN TOTAL DE {{ filteredProducts.length }} PLATILLOS REGISTRADOS.
                </div>
            </div>
            
        </div>
    </AdminLayout>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>