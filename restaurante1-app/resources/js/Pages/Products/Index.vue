<script setup>
/**
 * IMPORTACIONES Y DEPENDENCIAS
 * Aquí importamos el Layout de Administrador personalizado y los componentes de Inertia.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue'; 
import { Head, Link } from '@inertiajs/vue3'; 

/**
 * PROPIEDADES (PROPS)
 * Recibimos la lista de 'products' enviada desde el ProductController.
 * Estos son los productos reales de la base de datos que mostraremos en la tabla.
 */
const props = defineProps({
    products: Array
});

/**
 * FORMATEADOR DE MONEDA
 * Función auxiliar para dar formato bonito a los precios (ej: $10.50)
 */
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};
</script>

<template>
    <!-- Define el título de la pestaña del navegador -->
    <Head title="QuickBite Express | Inventory" />

    <!-- Envolvemos todo el contenido dentro del nuevo Layout de Administrador -->
    <AdminLayout>
        
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-10">
            
            <!-- HEADER DE LA PÁGINA -->
            <!-- Muestra el título y una breve descripción del área de inventario -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-black italic uppercase tracking-tighter text-red-700 font-['Epilogue'] flex items-center gap-2">
                        <span class="material-symbols-outlined text-3xl">inventory_2</span>
                        INVENTORY MANAGEMENT
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        Administra todos los platillos disponibles en el menú de QuickBite Express. 
                        Puedes agregar, editar o retirar productos del sistema central.
                    </p>
                </div>
                
                <!-- BOTÓN PRINCIPAL DE AGREGAR PRODUCTO -->
                <!-- Este botón nos lleva al formulario de creación -->
                <Link :href="route('products.create')" class="bg-red-700 text-white px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest flex items-center gap-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all shrink-0">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span> 
                    ADD NEW PRODUCT
                </Link>
            </div>

            <!-- CONTENEDOR DE LA TABLA DE PRODUCTOS -->
            <!-- Se utiliza un borde grueso y una sombra brutalista para mantener la estética High-Velocity -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,0.05)]">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <!-- CABECERA DE LA TABLA -->
                        <thead class="text-[11px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-900 bg-slate-50">
                            <tr>
                                <th class="px-6 py-5">PRODUCT NAME</th>
                                <th class="px-6 py-5">CATEGORY ID</th>
                                <th class="px-6 py-5">PRICE</th>
                                <th class="px-6 py-5 text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        
                        <!-- CUERPO DE LA TABLA -->
                        <tbody class="divide-y divide-slate-200 font-medium">
                            
                            <!-- Bucle que recorre cada producto recibido desde la base de datos -->
                            <tr v-for="product in products" :key="product.id" class="hover:bg-yellow-50 transition-colors group">
                                
                                <!-- COLUMNA: NOMBRE Y DESCRIPCIÓN -->
                                <td class="px-6 py-4">
                                    <p class="font-black text-[15px] text-slate-900 leading-tight font-['Epilogue']">
                                        {{ product.name }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1 max-w-sm">
                                        {{ product.description }}
                                    </p>
                                </td>
                                
                                <!-- COLUMNA: CATEGORÍA -->
                                <!-- Por ahora muestra el ID, idealmente aquí iría el nombre de la categoría si se hiciera un "join" en la base de datos -->
                                <td class="px-6 py-4">
                                    <span class="bg-slate-100 text-slate-600 px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border border-slate-300">
                                        Cat: {{ product.category_id }}
                                    </span>
                                </td>
                                
                                <!-- COLUMNA: PRECIO -->
                                <td class="px-6 py-4">
                                    <span class="font-black text-red-700 text-lg font-['Epilogue']">
                                        {{ formatPrice(product.price) }}
                                    </span>
                                </td>
                                
                                <!-- COLUMNA: ACCIONES -->
                                <!-- Botones de edición y eliminación (con funcionalidad base en maquetación) -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-50 group-hover:opacity-100 transition-opacity">
                                        
                                        <!-- Botón Editar (Placeholder visual) -->
                                        <button class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-yellow-400 text-slate-900 flex items-center justify-center hover:bg-yellow-500 transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                        </button>
                                        
                                        <!-- Botón Eliminar (Placeholder visual) -->
                                        <button class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-red-700 text-white flex items-center justify-center hover:bg-red-800 transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- ESTADO VACÍO -->
                            <!-- Se muestra si la base de datos no tiene productos -->
                            <tr v-if="products.length === 0">
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">inventory_2</span>
                                    <p class="text-slate-500 font-bold text-lg">El inventario está vacío.</p>
                                    <p class="text-slate-400 text-sm mt-1">Haz clic en "ADD NEW PRODUCT" para empezar.</p>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                
                <!-- FOOTER DE LA TABLA -->
                <div class="bg-slate-50 border-t-2 border-slate-900 p-4 text-xs font-bold text-slate-500 text-center">
                    Mostrando un total de {{ products.length }} platillos registrados.
                </div>
            </div>
            
        </div>
    </AdminLayout>
</template>