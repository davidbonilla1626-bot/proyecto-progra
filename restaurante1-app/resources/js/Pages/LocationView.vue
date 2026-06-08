<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';

const page = usePage();
</script>

<template>
    <Head title="Nuestra Ubicación y Horarios" />

    <div class="min-h-screen bg-[#f4f4f0] text-slate-900 pb-20 font-['Be_Vietnam_Pro'] selection:bg-[#ffcc00] selection:text-slate-900">
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-6xl mx-auto px-6 mt-12">
            <!-- TITULO -->
            <div class="mb-12 text-center md:text-left">
                <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tighter italic font-['Epilogue'] text-slate-950">
                    Nuestra Sucursal
                </h1>
                <p class="font-bold text-slate-500 mt-2">
                    Visítanos en nuestro local físico para disfrutar de la mejor comida rápida caliente al instante.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                <!-- COLUMNA IZQUIERDA: DATOS Y HORARIOS (5 columnas) -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- CARD UBICACION -->
                    <div class="border-4 border-slate-900 bg-white p-6 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="w-12 h-12 bg-red-100 rounded-xl border-2 border-slate-900 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-red-700 font-bold">location_on</span>
                        </div>
                        <h2 class="text-xl font-black uppercase tracking-tight text-slate-950">Dirección</h2>
                        <p class="text-sm text-slate-700 font-bold mt-2 leading-relaxed">
                            Calle a Quelepa, Km 139, campus de la Universidad Gerardo Barrios (UGB), San Miguel, El Salvador.
                        </p>
                    </div>

                    <!-- CARD HORARIOS -->
                    <div class="border-4 border-slate-900 bg-[#ffcc00] p-6 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="w-12 h-12 bg-white rounded-xl border-2 border-slate-900 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-slate-950 font-bold">schedule</span>
                        </div>
                        <h2 class="text-xl font-black uppercase tracking-tight text-slate-950">Horas de Atención</h2>
                        <div class="text-sm text-slate-950 font-black mt-2 space-y-1">
                            <p>Lunes a Domingo</p>
                            <p class="text-lg text-red-800 font-black mt-1">
                                {{ $page.props.shop.opening_time }} - {{ $page.props.shop.closing_time }}
                            </p>
                            <p class="text-xs text-slate-800 font-bold mt-2">
                                * Los pedidos se cierran automáticamente fuera de este horario.
                            </p>
                        </div>
                    </div>

                    <!-- CARD CONTACTO -->
                    <div class="border-4 border-slate-900 bg-white p-6 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl border-2 border-slate-900 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-emerald-700 font-bold">call</span>
                        </div>
                        <h2 class="text-xl font-black uppercase tracking-tight text-slate-950">Contacto Directo</h2>
                        <p class="text-sm text-slate-700 font-bold mt-2">
                            Teléfono: +503 2661-8200
                        </p>
                        <p class="text-sm text-slate-700 font-bold mt-0.5">
                            Soporte: soporte@quickbite-express.com
                        </p>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: MAPA (7 columnas) -->
                <div class="lg:col-span-7">
                    <div class="border-4 border-slate-900 bg-white rounded-3xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden p-2">
                        <iframe 
                            src="https://maps.google.com/maps?q=Universidad%20Gerardo%20Barrios,%20San%20Miguel,%20El%20Salvador&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                            width="100%" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="rounded-2xl border-2 border-slate-200"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
