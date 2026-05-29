<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: Array
});

const page = usePage();

// Formulario para Crear / Editar Categoría
const form = useForm({
    name: '',
    icon_path: 'restaurant' // Por defecto
});

// Control de Edición
const editingCategory = ref(null);
const isEditing = ref(false);

const startEdit = (cat) => {
    editingCategory.value = cat;
    form.name = cat.name;
    form.icon_path = cat.icon_path || 'restaurant';
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    editingCategory.value = null;
    form.reset();
};

// Guardar (Crear o Actualizar)
const submit = () => {
    if (isEditing.value) {
        form.put(route('categories.update', editingCategory.value.id), {
            onSuccess: () => {
                cancelEdit();
            }
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => {
                form.reset();
            }
        });
    }
};

// Eliminar
const deleteCategory = (id, name) => {
    if (confirm(`¿Estás seguro de que deseas eliminar la categoría "${name}"?`)) {
        router.delete(route('categories.destroy', id), {
            preserveScroll: true,
            onError: (errors) => {
                alert(errors.error || 'No se pudo eliminar la categoría.');
            }
        });
    }
};

</script>

<template>
    <Head title="Gestión de Categorías | Panel Administrativo" />

    <AdminLayout>
        <div class="p-8 md:p-12 max-w-6xl mx-auto space-y-10">
            
            <!-- ENCABEZADO -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 border-b-2 border-slate-200 pb-6">
                <div>
                    <h2 class="text-xl md:text-3xl font-black italic uppercase tracking-tighter text-red-700 font-['Epilogue'] flex items-center gap-2">
                        <span class="material-symbols-outlined text-3xl">category</span>
                        GESTIÓN DE CATEGORÍAS
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed font-bold">
                        Organiza el menú de QuickBite Express agregando, modificando o eliminando secciones del catálogo de comida.
                    </p>
                </div>
            </div>

            <!-- Mostrar errores flash de Laravel (como error al borrar categoría con productos) -->
            <div v-if="page.props.flash?.error" class="p-4 bg-red-100 border-2 border-red-300 rounded-2xl text-red-800 font-bold text-sm">
                {{ page.props.flash.error }}
            </div>
            <div v-if="page.props.flash?.message" class="p-4 bg-green-100 border-2 border-green-300 rounded-2xl text-green-800 font-bold text-sm">
                {{ page.props.flash.message }}
            </div>

            <!-- CONTENEDOR GRID: FORMULARIO (IZQUIERDA) & LISTADO (DERECHA) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
                
                <!-- 1. FORMULARIO CREAR / EDITAR -->
                <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                    <h3 class="text-md font-black uppercase tracking-widest text-slate-900 border-b border-slate-100 pb-2 font-['Epilogue']">
                        {{ isEditing ? 'EDITAR CATEGORÍA' : 'NUEVA CATEGORÍA' }}
                    </h3>

                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Nombre de Categoría *</label>
                            <input 
                                v-model="form.name"
                                type="text"
                                placeholder="Ej. Postres"
                                class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-2.5 text-sm font-bold bg-white text-slate-900"
                                required
                            />
                            <p v-if="form.errors.name" class="text-red-600 text-xs mt-1 font-bold">{{ form.errors.name }}</p>
                        </div>

                        <!-- Icono path (Material icons key) -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Icono (Material Symbols) *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold">
                                    {{ form.icon_path }}
                                </span>
                                <input 
                                    v-model="form.icon_path"
                                    type="text"
                                    placeholder="Ej. restaurant_menu, eco, local_drink"
                                    class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors pl-10 pr-4 py-2.5 text-sm font-bold bg-white text-slate-900"
                                    required
                                />
                            </div>
                            <p class="text-[9px] text-slate-400 mt-1 font-semibold uppercase">
                                Escribe palabras clave de Material Icons como: icecream, cake, local_pizza, soup_kitchen.
                            </p>
                        </div>

                        <!-- Botones -->
                        <div class="pt-4 flex flex-col gap-2">
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-[#ffcc00] hover:bg-yellow-500 text-slate-950 font-black py-3 rounded-xl border-2 border-slate-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all text-xs uppercase tracking-widest"
                            >
                                {{ isEditing ? 'Guardar Cambios' : 'Agregar Sección' }}
                            </button>
                            <button 
                                v-if="isEditing"
                                type="button"
                                @click="cancelEdit"
                                class="w-full bg-white hover:bg-slate-50 text-slate-500 font-black py-3 rounded-xl border-2 border-slate-900 transition-all text-xs uppercase tracking-widest"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. TABLA / LISTADO -->
                <div class="lg:col-span-2 bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[11px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-900 bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4">Icono</th>
                                    <th class="px-6 py-4">Categoría</th>
                                    <th class="px-6 py-4 text-center">Platillos</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-bold">
                                <tr v-for="cat in categories" :key="cat.id" class="hover:bg-yellow-50 transition-colors">
                                    
                                    <!-- Icono -->
                                    <td class="px-6 py-4">
                                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 border-2 border-slate-900 flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <span class="material-symbols-outlined text-[20px] font-bold">{{ cat.icon_path || 'restaurant' }}</span>
                                        </div>
                                    </td>

                                    <!-- Nombre -->
                                    <td class="px-6 py-4 font-black text-slate-900 text-base font-['Epilogue']">
                                        {{ cat.name }}
                                    </td>

                                    <!-- Cantidad de productos -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-slate-100 text-slate-600 px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border border-slate-300">
                                            {{ cat.products_count || 0 }} productos
                                        </span>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <!-- Editar -->
                                            <button @click="startEdit(cat)" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-yellow-400 text-slate-900 flex items-center justify-center hover:bg-yellow-500 transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]" title="Editar">
                                                <span class="material-symbols-outlined text-[16px] font-bold">edit</span>
                                            </button>
                                            
                                            <!-- Eliminar -->
                                            <button @click="deleteCategory(cat.id, cat.name)" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-red-700 text-white flex items-center justify-center hover:bg-red-800 transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] cursor-pointer" title="Eliminar">
                                                <span class="material-symbols-outlined text-[16px] font-bold">delete</span>
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </AdminLayout>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
