<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

// Modal control
const showModal = ref(false);
const isEditing = ref(false);
const editingUserId = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user'
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    showModal.value = true;
};

const openEditModal = (user) => {
    form.reset();
    form.clearErrors();
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = ''; // Opcional
    editingUserId.value = user.id;
    isEditing.value = true;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('users.update', editingUserId.value), {
            onSuccess: () => {
                closeModal();
            }
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => {
                closeModal();
            }
        });
    }
};

const deleteUser = (userId) => {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción es irreversible.')) {
        router.delete(route('users.destroy', userId));
    }
};

const getRoleBadgeClass = (role) => {
    switch (role) {
        case 'admin':
            return 'bg-red-100 text-red-800 border-red-300';
        case 'employee':
            return 'bg-blue-100 text-blue-800 border-blue-300';
        default:
            return 'bg-slate-100 text-slate-800 border-slate-300';
    }
};
</script>

<template>
    <Head title="Gestión de Usuarios | Panel Administrativo" />

    <AdminLayout>
        <div class="p-8 md:p-12 max-w-7xl mx-auto space-y-10">
            
            <!-- Encabezado -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 border-b-2 border-slate-200 pb-6">
                <div>
                    <h2 class="text-xl md:text-3xl font-black italic uppercase tracking-tighter text-red-700 font-['Epilogue'] flex items-center gap-2">
                        <span class="material-symbols-outlined text-3xl">group</span>
                        GESTIÓN DE USUARIOS
                    </h2>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed font-bold">
                        Administra las cuentas del sistema. Crea administradores, empleados de cocina y consulta los datos de los clientes.
                    </p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="bg-[#ffcc00] text-slate-950 border-2 border-slate-900 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all flex items-center gap-2"
                >
                    <span class="material-symbols-outlined text-sm font-bold">person_add</span>
                    NUEVO USUARIO
                </button>
            </div>

            <!-- Tabla de Usuarios (Diseño Brutalista) -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <div v-if="users.length === 0" class="text-center py-20">
                    <span class="material-symbols-outlined text-5xl text-slate-300">group</span>
                    <p class="text-slate-400 text-lg font-black mt-2">No hay usuarios registrados.</p>
                </div>
                
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-slate-500 uppercase font-black tracking-widest border-b-2 border-slate-900 bg-slate-50">
                            <tr>
                                <th class="px-6 py-5">Nombre</th>
                                <th class="px-6 py-5">Correo Electrónico</th>
                                <th class="px-6 py-5">Rol</th>
                                <th class="px-6 py-5">Fecha de Registro</th>
                                <th class="px-6 py-5 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-bold">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-yellow-50 transition-colors">
                                <td class="px-6 py-4 text-slate-900 font-black font-['Epilogue']">{{ user.name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-[10px] font-black tracking-widest rounded-lg border-2 uppercase"
                                          :class="getRoleBadgeClass(user.role)">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-normal text-xs">
                                    {{ new Date(user.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' }) }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button 
                                        @click="openEditModal(user)"
                                        class="bg-white text-slate-900 border-2 border-slate-900 hover:bg-slate-50 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
                                    >
                                        Editar
                                    </button>
                                    <button 
                                        v-if="$page.props.auth.user.id !== user.id"
                                        @click="deleteUser(user.id)"
                                        class="bg-red-700 text-white border-2 border-slate-900 hover:bg-red-800 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL DE CREACIÓN / EDICIÓN -->
        <div v-if="showModal" class="fixed inset-0 bg-slate-950/80 flex items-center justify-center p-4 z-50 animate-fadeIn">
            <div class="bg-white border-4 border-slate-900 rounded-3xl p-6 md:p-8 max-w-md w-full shadow-[8px_8px_0px_0px_rgba(255,204,0,1)] relative animate-scaleUp">
                
                <button @click="closeModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-950 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-3xl font-black">close</span>
                </button>

                <div class="border-b-2 border-slate-900 pb-4 mb-6">
                    <h3 class="text-2xl font-black italic uppercase font-['Epilogue'] tracking-tighter text-slate-950">
                        {{ isEditing ? 'EDITAR USUARIO' : 'CREAR USUARIO' }}
                    </h3>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">Nombre completo</label>
                        <input v-model="form.name" required type="text" placeholder="Ej. Juan Pérez" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 px-4 py-3 text-sm font-bold bg-white text-slate-900">
                        <div v-if="form.errors.name" class="text-red-600 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">Correo electrónico</label>
                        <input v-model="form.email" required type="email" placeholder="ejemplo@restaurante.com" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 px-4 py-3 text-sm font-bold bg-white text-slate-900">
                        <div v-if="form.errors.email" class="text-red-600 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">
                            Contraseña {{ isEditing ? '(Dejar vacío para mantener actual)' : '' }}
                        </label>
                        <input v-model="form.password" :required="!isEditing" type="password" placeholder="Contraseña de acceso" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 px-4 py-3 text-sm font-bold bg-white text-slate-900">
                        <div v-if="form.errors.password" class="text-red-600 text-xs mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">Rol de usuario</label>
                        <select v-model="form.role" required class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 px-4 py-3 text-sm font-bold bg-white text-slate-900">
                            <option value="user">Cliente (user)</option>
                            <option value="employee">Cocinero / Personal (employee)</option>
                            <option value="admin">Administrador (admin)</option>
                        </select>
                        <div v-if="form.errors.role" class="text-red-600 text-xs mt-1">{{ form.errors.role }}</div>
                    </div>

                    <div class="pt-4 border-t-2 border-slate-200 flex gap-4">
                        <button 
                            type="button"
                            @click="closeModal"
                            class="w-1/2 bg-white text-slate-900 hover:bg-slate-50 font-black py-4 rounded-xl text-xs uppercase tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)] transition-all cursor-pointer text-center"
                        >
                            CANCELAR
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="w-1/2 bg-[#ffcc00] hover:bg-yellow-500 text-slate-950 font-black py-4 rounded-xl text-xs uppercase tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all cursor-pointer text-center"
                        >
                            {{ isEditing ? 'GUARDAR' : 'CREAR' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </AdminLayout>
</template>
