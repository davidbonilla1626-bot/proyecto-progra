<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Recibimos las categorías para el select
defineProps({
    categories: Array
});

// Iniciamos el formulario con useForm de Inertia para manejar validaciones
const form = useForm({
    name: '',
    description: '',
    price: '',
    category_id: '',
    image_path: 'https://via.placeholder.com/150' // Valor por defecto temporal
});

const submit = () => {
    form.post(route('products.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Crear Producto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Añadir Nuevo Plato</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="name" value="Nombre del Plato" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Categoría (Relación de Entidades) -->
                        <div class="mt-4">
                            <InputLabel for="category_id" value="Categoría" />
                            <select id="category_id" v-model="form.category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required>
                                <option value="" disabled>Selecciona una categoría</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.category_id" />
                        </div>

                        <!-- Precio -->
                        <div class="mt-4">
                            <InputLabel for="price" value="Precio ($)" />
                            <TextInput id="price" type="number" step="0.01" class="mt-1 block w-full" v-model="form.price" required />
                            <InputError class="mt-2" :message="form.errors.price" />
                        </div>

                        <!-- Descripción -->
                        <div class="mt-4">
                            <InputLabel for="description" value="Descripción" />
                            <textarea id="description" v-model="form.description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" rows="3"></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Guardar Plato
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>