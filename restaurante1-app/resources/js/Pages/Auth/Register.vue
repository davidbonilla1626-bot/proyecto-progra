<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    admin_key: '',
});

const showAdminField = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrarse | QuickBite Express" />

        <div class="mb-6 text-center">
            <h1 class="text-3xl font-black italic uppercase text-red-700 tracking-tighter font-['Epilogue']">
                Únete a QuickBite
            </h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Registra tu cuenta de cliente en segundos.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Nombre -->
            <div>
                <InputLabel for="name" value="Nombre Completo" class="text-xs font-black tracking-widest text-slate-700 uppercase" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full border-2 border-slate-900 rounded-xl px-4 py-3 focus:ring-red-500 focus:border-red-700 font-bold"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Ej. Juan Pérez"
                    autocomplete="name"
                />

                <InputError class="mt-1 font-bold text-xs" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Correo Electrónico" class="text-xs font-black tracking-widest text-slate-700 uppercase" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-2 border-slate-900 rounded-xl px-4 py-3 focus:ring-red-500 focus:border-red-700 font-bold"
                    v-model="form.email"
                    required
                    placeholder="Ej. juan@correo.com"
                    autocomplete="username"
                />

                <InputError class="mt-1 font-bold text-xs" :message="form.errors.email" />
            </div>

            <!-- Contraseña -->
            <div>
                <InputLabel for="password" value="Contraseña" class="text-xs font-black tracking-widest text-slate-700 uppercase" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full border-2 border-slate-900 rounded-xl px-4 py-3 focus:ring-red-500 focus:border-red-700 font-bold"
                    v-model="form.password"
                    required
                    placeholder="Mínimo 8 caracteres"
                    autocomplete="new-password"
                />

                <InputError class="mt-1 font-bold text-xs" :message="form.errors.password" />
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Contraseña"
                    class="text-xs font-black tracking-widest text-slate-700 uppercase"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full border-2 border-slate-900 rounded-xl px-4 py-3 focus:ring-red-500 focus:border-red-700 font-bold"
                    v-model="form.password_confirmation"
                    required
                    placeholder="Repite tu contraseña"
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-1 font-bold text-xs"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <!-- Registro Administrativo Opcional -->
            <div class="border-t-2 border-dashed border-slate-200 pt-4">
                <button 
                    type="button"
                    @click="showAdminField = !showAdminField"
                    class="text-xs font-bold text-red-700 hover:text-red-900 flex items-center gap-1 focus:outline-none"
                >
                    <span class="material-symbols-outlined text-[16px]">
                        {{ showAdminField ? 'arrow_drop_up' : 'arrow_drop_down' }}
                    </span>
                    ¿Tienes un código de administrador?
                </button>

                <div v-show="showAdminField" class="mt-3 bg-red-50 p-4 border-2 border-red-200 rounded-xl animate-fadeIn">
                    <InputLabel
                        for="admin_key"
                        value="Código Secreto de Administrador"
                        class="text-[10px] font-black tracking-widest text-red-800 uppercase"
                    />
                    <TextInput
                        id="admin_key"
                        type="password"
                        class="mt-1 block w-full border-2 border-red-300 rounded-xl px-4 py-2 focus:ring-red-500 focus:border-red-700 font-bold bg-white text-red-950"
                        v-model="form.admin_key"
                        placeholder="Ingresa clave para rol ADMIN"
                    />
                    <p class="text-[10px] text-red-600 mt-1 font-bold">
                        Solo si eres parte del equipo administrativo de QuickBite Express.
                    </p>
                    <InputError class="mt-1 font-bold text-xs" :message="form.errors.admin_key" />
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                <Link
                    :href="route('login')"
                    class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-900 transition-colors underline"
                >
                    ¿Ya estás registrado? Inicia Sesión
                </Link>

                <button
                    class="w-full sm:w-auto bg-[#ffcc00] hover:bg-yellow-500 text-slate-950 px-6 py-3.5 rounded-xl border-2 border-slate-900 font-black text-xs uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    REGISTRARSE
                </button>
            </div>
        </form>
    </GuestLayout>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
