<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión | QuickBite Express" />

    <div class="bg-[#f8f9fa] text-[#191c1d] min-h-screen font-['Be_Vietnam_Pro'] pb-20 relative">
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-md mx-auto pt-16 px-4">
            
            <!-- HEADER DEL FORMULARIO -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-black text-[#b7102a] italic tracking-tighter uppercase font-['Epilogue'] leading-none">
                    INICIA SESIÓN
                </h1>
                <p class="text-slate-500 font-medium mt-2">¡El sabor a alta velocidad está a un clic!</p>
            </div>

            <!-- TARJETA DE LOGIN -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl shadow-[6px_6px_0px_0px_rgba(25,28,29,1)] overflow-hidden">
                
                <!-- TABS -->
                <div class="flex border-b-2 border-slate-900">
                    <div class="w-1/2 bg-[#ffcc00] border-r-2 border-slate-900 py-4 text-center font-black text-lg text-slate-900 cursor-pointer">
                        Ingresar
                    </div>
                    <Link :href="route('register')" class="w-1/2 bg-white py-4 text-center font-bold text-lg text-slate-400 cursor-pointer hover:bg-slate-50 transition-colors block">
                        Registrarse
                    </Link>
                </div>

                <div class="p-8">
                    <!-- MENSAJE DE ESTADO (Por ejemplo, recuperación de contraseña) -->
                    <div v-if="status" class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-xl text-green-700 text-sm font-bold animate-fadeIn">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <!-- EMAIL -->
                        <div>
                            <label for="email" class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Correo Electrónico</label>
                            <input
                                id="email"
                                type="email"
                                class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold outline-none text-slate-900"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="tu@correo.com"
                            />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.email }}</p>
                        </div>

                        <!-- PASSWORD -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="password" class="block text-xs font-black uppercase tracking-widest text-slate-600">Contraseña</label>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs font-bold text-[#b7102a] hover:underline"
                                >
                                    ¿La olvidaste?
                                </Link>
                            </div>
                            
                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold outline-none text-slate-900"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                            </div>
                            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.password }}</p>
                        </div>

                        <!-- REMEMBER ME -->
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                v-model="form.remember" 
                                class="rounded border-slate-900 text-red-700 focus:ring-red-500 size-4 cursor-pointer"
                            />
                            <label for="remember" class="ml-2 text-xs font-bold text-slate-600 uppercase tracking-widest cursor-pointer select-none">
                                Recordar sesión
                            </label>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="pt-4">
                            <button
                                type="submit"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing"
                                class="w-full bg-[#b7102a] hover:bg-red-800 text-white border-2 border-slate-900 rounded-xl py-4 text-xl italic font-black uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[4px] hover:translate-y-[4px] transition-all font-['Epilogue'] cursor-pointer"
                            >
                                ¡A COMER!
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- FOOTER -->
                <div class="bg-slate-50 border-t-2 border-slate-900 p-5 text-center">
                    <p class="text-[10px] text-slate-500 font-medium">
                        Al iniciar sesión, aceptas nuestros 
                        <a href="#" class="text-[#b7102a] hover:underline font-bold">Términos de Servicio</a> y 
                        <a href="#" class="text-[#b7102a] hover:underline font-bold">Política de Privacidad</a>.
                    </p>
                </div>
            </div>
            
        </div>
        
        <!-- DECORACIÓN INFERIOR (Franjas) -->
        <div class="fixed bottom-0 left-0 w-full z-50">
            <div class="w-full h-2 bg-slate-900"></div>
            <div class="w-full h-3 bg-[#b7102a]"></div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
