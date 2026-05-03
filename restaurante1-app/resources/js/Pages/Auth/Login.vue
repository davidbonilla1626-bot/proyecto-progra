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
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="bg-[#f8f9fa] text-[#191c1d] min-h-screen font-['Be_Vietnam_Pro'] pb-20 relative">
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-md mx-auto pt-16 px-4">
            
            <!-- HEADER DEL FORMULARIO -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-black text-[#b7102a] italic tracking-tighter uppercase font-['Epilogue'] leading-none">
                    JOIN THE EXPRESS
                </h1>
                <p class="text-slate-500 font-medium mt-2">High-velocity flavor is just a click away.</p>
            </div>

            <!-- TARJETA DE LOGIN -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl shadow-[6px_6px_0px_0px_rgba(25,28,29,1)] overflow-hidden">
                
                <!-- TABS -->
                <div class="flex border-b-2 border-slate-900">
                    <div class="w-1/2 bg-[#ffcc00] border-r-2 border-slate-900 py-4 text-center font-black text-lg text-slate-900 cursor-pointer">
                        Login
                    </div>
                    <div class="w-1/2 bg-white py-4 text-center font-bold text-lg text-slate-400 cursor-pointer hover:bg-slate-50 transition-colors">
                        Sign Up
                    </div>
                </div>

                <div class="p-8">
                    <!-- SOCIAL BUTTONS (Ejemplo estético) -->
                    <div class="space-y-3 mb-8">
                        <button type="button" class="w-full flex items-center justify-center gap-2 border-2 border-slate-300 rounded-xl py-3 font-bold text-sm text-slate-700 hover:bg-slate-50 transition-colors hover:border-slate-400">
                            <span class="text-[#EA4335] font-black text-lg">G</span> Continue with Google
                        </button>
                        
                        <button type="button" class="w-full flex items-center justify-center gap-2 border-2 border-[#1877F2] bg-[#1877F2] rounded-xl py-3 font-bold text-sm text-white hover:bg-[#166fe5] transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
                            <span class="font-black text-white text-lg">f</span> Continue with Facebook
                        </button>
                    </div>

                    <!-- DIVIDER -->
                    <div class="relative flex py-5 items-center">
                        <div class="flex-grow border-t border-slate-200"></div>
                        <span class="flex-shrink-0 mx-4 text-[10px] font-black tracking-widest text-slate-400 uppercase">Or Email</span>
                        <div class="flex-grow border-t border-slate-200"></div>
                    </div>

                    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <!-- EMAIL -->
                        <div>
                            <label for="email" class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Email Address</label>
                            <input
                                id="email"
                                type="email"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm outline-none text-slate-900"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="speedy@quickbite.com"
                            />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.email }}</p>
                        </div>

                        <!-- PASSWORD -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="password" class="block text-xs font-black uppercase tracking-widest text-slate-600">Password</label>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs font-bold text-[#b7102a] hover:underline"
                                >
                                    Forgot?
                                </Link>
                            </div>
                            
                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    class="w-full rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm outline-none text-slate-900"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.password }}</p>
                        </div>

                        <!-- REMEMBER ME (Oculto visualmente en el mockup, se asume true o se ignora en UI) -->
                        <div class="hidden">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" v-model="form.remember" />
                                <span>Remember me</span>
                            </label>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="pt-4">
                            <button
                                type="submit"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing"
                                class="w-full bg-[#b7102a] text-white border-2 border-slate-900 rounded-xl py-4 text-xl italic font-black uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[4px] hover:translate-y-[4px] transition-all font-['Epilogue']"
                            >
                                LET'S EAT!
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- FOOTER -->
                <div class="bg-slate-50 border-t-2 border-slate-900 p-5 text-center">
                    <p class="text-[10px] text-slate-500 font-medium">
                        By logging in, you agree to our 
                        <a href="#" class="text-[#b7102a] hover:underline">Terms of Service</a> and 
                        <a href="#" class="text-[#b7102a] hover:underline">Privacy Policy</a>.
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
