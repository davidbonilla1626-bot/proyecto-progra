<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
    categories: Array
});

// Selector de método de imagen: 'file' o 'url'
const imageSource = ref(props.product.image && props.product.image.startsWith('http') ? 'url' : 'file');

const form = useForm({
    _method: 'PUT', // Truco de Laravel para soportar subida de archivos con método PUT
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    stock: props.product.stock,
    category_id: props.product.category_id,
    image: null,
    image_url: props.product.image && props.product.image.startsWith('http') ? props.product.image : ''
});

const submit = () => {
    form.post(route('products.update', props.product.id), {
        forceFormData: true // Requerido para enviar el archivo
    });
};

const handleFileChange = (e) => {
    form.image = e.target.files[0];
};

</script>

<template>
    <Head :title="`Editar ${product.name} | Panel Administrativo`" />

    <AdminLayout>
        <div class="p-8 md:p-12 max-w-4xl mx-auto space-y-10">
            
            <!-- HEADER CON BOTÓN DE REGRESO -->
            <div class="flex flex-col gap-4">
                <Link :href="route('products.index')" class="flex items-center gap-1 text-sm font-black uppercase tracking-widest text-slate-500 hover:text-red-700 transition-colors w-fit">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    VOLVER A INVENTARIO
                </Link>
                
                <h2 class="text-3xl md:text-4xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] flex items-center gap-3">
                    <span class="material-symbols-outlined text-4xl text-yellow-500">edit_note</span>
                    EDITAR PRODUCTO: {{ product.name }}
                </h2>
                <p class="text-slate-600 text-sm max-w-2xl leading-relaxed font-bold">
                    Modifica los campos del platillo en el sistema. Todos los campos marcados con (*) son obligatorios.
                </p>
            </div>

            <!-- FORMULARIO BRUTALISTA -->
            <div class="bg-white border-2 border-slate-900 rounded-3xl p-8 md:p-10 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                
                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- DETALLES BÁSICOS -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-black uppercase tracking-widest text-slate-900 border-b-2 border-slate-200 pb-2">
                            Información del Platillo
                        </h3>

                        <!-- Nombre -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Nombre del Producto *
                            </label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold text-slate-900 bg-white" 
                                required
                            >
                            <p v-if="form.errors.name" class="text-red-600 text-xs mt-1 font-bold">{{ form.errors.name }}</p>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Descripción e Ingredientes *
                            </label>
                            <textarea 
                                v-model="form.description" 
                                class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold text-slate-900 bg-white resize-none" 
                                rows="4"
                                required
                            ></textarea>
                            <p v-if="form.errors.description" class="text-red-600 text-xs mt-1 font-bold">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <!-- PRECIO, STOCK Y CATEGORÍA -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Precio -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Precio ($) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-black material-symbols-outlined text-lg">attach_money</span>
                                <input 
                                    v-model="form.price" 
                                    type="number" 
                                    step="0.01" 
                                    class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors pl-10 pr-4 py-3 text-lg font-black text-slate-900 font-['Epilogue'] bg-white" 
                                    required
                                >
                            </div>
                            <p v-if="form.errors.price" class="text-red-600 text-xs mt-1 font-bold">{{ form.errors.price }}</p>
                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Stock Disponible *
                            </label>
                            <input 
                                v-model="form.stock" 
                                type="number" 
                                class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3.5 text-sm font-bold text-slate-900 bg-white" 
                                required
                            >
                            <p v-if="form.errors.stock" class="text-red-600 text-xs mt-1 font-bold">{{ form.errors.stock }}</p>
                        </div>

                        <!-- Categoría -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">
                                Categoría del Menú *
                            </label>
                            <div class="relative">
                                <select 
                                    v-model="form.category_id" 
                                    class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3.5 appearance-none text-sm font-bold text-slate-900 bg-white cursor-pointer" 
                                    required
                                >
                                    <option value="" disabled>Selecciona una categoría...</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500 font-bold">expand_more</span>
                            </div>
                            <p v-if="form.errors.category_id" class="text-red-600 text-xs mt-1 font-bold">{{ form.errors.category_id }}</p>
                        </div>

                    </div>

                    <!-- GESTIÓN DE IMAGEN -->
                    <div class="space-y-6 border-t border-slate-100 pt-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-600">
                                Imagen del Producto
                            </h3>
                            <!-- Preview de imagen actual -->
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-400 font-bold">Imagen Actual:</span>
                                <img :src="product.image" class="w-12 h-12 rounded-lg border border-slate-900 object-cover shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                        </div>

                        <!-- Toggles para el tipo de origen de la imagen -->
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-sm text-slate-700 uppercase tracking-wide">
                                <input type="radio" v-model="imageSource" value="url" class="text-red-700 focus:ring-red-500 border-slate-900">
                                Enlace de Internet (URL)
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-sm text-slate-700 uppercase tracking-wide">
                                <input type="radio" v-model="imageSource" value="file" class="text-red-700 focus:ring-red-500 border-slate-900">
                                Subir archivo local
                            </label>
                        </div>

                        <!-- Input URL -->
                        <div v-if="imageSource === 'url'">
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Dirección URL de la Imagen</label>
                            <input 
                                v-model="form.image_url" 
                                type="url" 
                                placeholder="Pega un enlace web (ej. Unsplash)" 
                                class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold text-slate-900 bg-white"
                            >
                        </div>

                        <!-- Input File -->
                        <div v-else class="border-2 border-dashed border-slate-300 rounded-2xl p-6 bg-slate-50 text-center hover:border-slate-900 transition-colors">
                            <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
                            <input 
                                type="file" 
                                @change="handleFileChange" 
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-2 file:border-slate-900 file:text-xs file:font-black file:bg-yellow-400 file:text-slate-900 hover:file:bg-yellow-500 cursor-pointer"
                                accept="image/*"
                            >
                            <p class="text-[10px] text-slate-400 mt-2 font-semibold">Formatos aceptados: JPG, PNG, WEBP. Tamaño máx: 2MB (Deja vacío para mantener la actual)</p>
                        </div>
                    </div>

                    <!-- BOTÓN DE ENVÍO -->
                    <div class="pt-6 border-t border-slate-200 flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-[#ffcc00] hover:bg-yellow-500 text-slate-900 px-8 py-4.5 rounded-xl font-black text-xs uppercase tracking-widest flex items-center gap-2 border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50"
                        >
                            <span class="material-symbols-outlined font-bold">save</span>
                            ACTUALIZAR PLATILLO
                        </button>
                    </div>

                </form>
            </div>
            
        </div>
    </AdminLayout>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
