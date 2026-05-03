<script setup>
/**
 * IMPORTACIONES Y DEPENDENCIAS
 * Aquí importamos el Layout de Administrador personalizado y dependencias de Inertia.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

/**
 * PROPIEDADES (PROPS)
 * Recibimos las categorías disponibles desde el controlador para poder mostrarlas 
 * en el menú desplegable (select) del formulario.
 */
defineProps({
    categories: Array
});

/**
 * ESTADO DEL FORMULARIO
 * Utilizamos el helper 'useForm' de Inertia para manejar los datos del formulario de manera reactiva.
 * Esto nos facilita el envío de datos y el manejo de errores o estados de carga.
 */
const form = useForm({
    name: '',
    description: '',
    price: '',
    category_id: '',
    image_path: 'default.jpg' // Imagen por defecto mientras no implementamos carga de archivos
});

/**
 * FUNCIÓN SUBMIT
 * Se ejecuta al enviar el formulario. Manda una petición POST a la ruta 'products.store'.
 */
const submit = () => {
    form.post(route('products.store'));
};
</script>

<template>
    <!-- Define el título de la pestaña del navegador -->
    <Head title="QuickBite Express | Add Product" />

    <!-- Envolvemos todo el contenido dentro del Layout de Administrador -->
    <AdminLayout>
        
        <div class="p-8 md:p-12 max-w-4xl mx-auto space-y-10">
            
            <!-- HEADER DE LA PÁGINA CON BOTÓN DE REGRESO -->
            <div class="flex flex-col gap-4">
                <!-- Enlace para volver al inventario -->
                <Link :href="route('products.index')" class="flex items-center gap-1 text-sm font-black uppercase tracking-widest text-slate-500 hover:text-red-700 transition-colors w-fit">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    BACK TO INVENTORY
                </Link>
                
                <!-- Título principal -->
                <h2 class="text-3xl md:text-4xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] flex items-center gap-3">
                    <span class="material-symbols-outlined text-4xl text-red-700">add_circle</span>
                    NEW PRODUCT CREATION
                </h2>
                <p class="text-slate-600 text-sm max-w-2xl leading-relaxed">
                    Añade un nuevo platillo al sistema. Todos los campos marcados con (*) son obligatorios.
                </p>
            </div>

            <!-- CONTENEDOR DEL FORMULARIO -->
            <!-- Usamos un borde grueso y sombra brutalista para la caja principal del formulario -->
            <div class="bg-white border-2 border-slate-900 rounded-3xl p-8 md:p-10 shadow-[8px_8px_0px_0px_rgba(0,0,0,0.05)]">
                
                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- SECCIÓN: DETALLES DEL PRODUCTO -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-black uppercase tracking-widest text-slate-900 border-b-2 border-slate-200 pb-2">
                            Product Details
                        </h3>

                        <!-- CAMPO: NOMBRE DEL PLATILLO -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Product Name <span class="text-red-700">*</span>
                            </label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                placeholder="e.g. Double Smash Burger" 
                                class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold text-slate-900 outline-none placeholder:font-normal placeholder:text-slate-400" 
                                required
                            >
                        </div>

                        <!-- CAMPO: DESCRIPCIÓN -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Description & Ingredients
                            </label>
                            <textarea 
                                v-model="form.description" 
                                placeholder="Describe the flavors, ingredients, and preparation..." 
                                class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm font-medium text-slate-900 outline-none resize-none placeholder:text-slate-400" 
                                rows="4"
                            ></textarea>
                        </div>
                    </div>

                    <!-- SECCIÓN: PRECIOS Y CATEGORÍA -->
                    <!-- Se divide en dos columnas en escritorio -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- CAMPO: PRECIO -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Price ($) <span class="text-red-700">*</span>
                            </label>
                            <div class="relative">
                                <!-- Ícono de moneda posicionado absolutamente -->
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-black material-symbols-outlined text-lg">attach_money</span>
                                <input 
                                    v-model="form.price" 
                                    type="number" 
                                    step="0.01" 
                                    placeholder="0.00"
                                    class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors pl-10 pr-4 py-3 text-lg font-black text-slate-900 font-['Epilogue'] outline-none" 
                                    required
                                >
                            </div>
                        </div>

                        <!-- CAMPO: CATEGORÍA -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Category <span class="text-red-700">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    v-model="form.category_id" 
                                    class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 appearance-none text-sm font-bold text-slate-900 outline-none bg-white cursor-pointer" 
                                    required
                                >
                                    <option value="" disabled selected>Select a category...</option>
                                    <!-- Iteramos las categorías recibidas del backend -->
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                                <!-- Flecha decorativa del select -->
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">expand_more</span>
                            </div>
                        </div>

                    </div>

                    <!-- BOTÓN DE ENVÍO -->
                    <!-- Bloqueamos el botón y reducimos su opacidad mientras se procesa la solicitud -->
                    <div class="pt-6 border-t border-slate-200 flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-yellow-400 text-slate-900 px-8 py-4 rounded-xl font-black text-[15px] uppercase tracking-widest flex items-center gap-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="material-symbols-outlined">save</span>
                            SAVE TO MENU
                        </button>
                    </div>

                </form>
            </div>
            
        </div>
    </AdminLayout>
</template>