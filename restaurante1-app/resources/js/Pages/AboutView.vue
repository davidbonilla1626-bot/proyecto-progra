<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
</script>

<template>
    <Head title="Nosotros | QuickBite Express" />

    <div class="bg-slate-50 min-h-screen font-['Be_Vietnam_Pro'] text-slate-900 pb-20">
        
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <!-- ÁREA CENTRAL DE CONTENIDO -->
        <main class="max-w-7xl mx-auto px-6 mt-12 space-y-20">
            
            <!-- HISTORIA Y MISIÓN -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                
                <!-- Columna Izquierda: Textos y Estadísticas -->
                <div class="space-y-6">
                    <h1 class="text-5xl md:text-6xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] leading-none">
                        NACIDOS PARA SER <br><span class="text-red-700">RÁPIDOS.</span>
                    </h1>
                    
                    <p class="text-lg text-slate-600 leading-relaxed font-bold">
                        QuickBite Express fue fundado en 2026 con una misión agresiva y clara: erradicar el hambre a la máxima velocidad sin comprometer ni un solo gramo de calidad. 
                    </p>
                    <p class="text-sm text-slate-500 leading-relaxed font-semibold">
                        Creemos que la comida rápida no tiene por qué ser comida de baja calidad. Nuestras cocinas operan como motores de alta precisión perfectamente afinados, sirviendo sabores de alto octanaje diseñados para el estilo de vida dinámico del mundo moderno.
                    </p>
                    
                    <!-- Tarjetas de Estadísticas -->
                    <div class="flex gap-4">
                        <div class="bg-[#ffcc00] border-2 border-slate-900 rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center flex-1">
                            <p class="text-3xl font-black font-['Epilogue']">10K+</p>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-900 mt-1">Hamburguesas Servidas</p>
                        </div>
                        <div class="bg-red-700 border-2 border-slate-900 rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center flex-1 text-white">
                            <p class="text-3xl font-black font-['Epilogue']">&lt; 15m</p>
                            <p class="text-[10px] font-black uppercase tracking-widest text-red-200 mt-1">Tiempo de Entrega</p>
                        </div>
                    </div>
                </div>
                
                <!-- Columna Derecha: Imagen decorativa -->
                <div class="h-[400px] border-4 border-slate-900 rounded-3xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(183,16,42,1)] bg-slate-200 relative group">
                    <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?w=800" alt="QuickBite Kitchen" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-red-700 mix-blend-overlay opacity-50"></div>
                </div>
            </section>

            <!-- HORARIOS Y UBICACIÓN -->
            <section class="bg-slate-900 text-white border-4 border-slate-900 rounded-3xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(255,204,0,1)] flex flex-col md:flex-row">
                
                <!-- Lado Izquierdo: Lista de Horarios -->
                <div class="p-10 md:p-12 md:w-1/3 space-y-8 flex flex-col justify-center border-b-4 md:border-b-0 md:border-r-4 border-slate-900">
                    <h2 class="text-3xl font-black italic uppercase tracking-tighter font-['Epilogue'] text-[#ffcc00]">Horarios y Ubicación</h2>
                    
                    <ul class="space-y-4 text-xs font-black tracking-widest uppercase">
                        <li class="flex justify-between border-b border-slate-700 pb-2">
                            <span class="text-slate-400">LUN - JUE</span>
                            <span>11:00 AM - 10:00 PM</span>
                        </li>
                        <li class="flex justify-between border-b border-slate-700 pb-2">
                            <span class="text-slate-400">VIE - SÁB</span>
                            <span class="text-[#ffcc00]">11:00 AM - 02:00 AM</span>
                        </li>
                        <li class="flex justify-between border-b border-slate-700 pb-2">
                            <span class="text-slate-400">DOMINGOS</span>
                            <span>12:00 PM - 09:00 PM</span>
                        </li>
                    </ul>

                    <!-- Dirección -->
                    <div class="text-xs font-bold">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Dirección General</p>
                        <p class="font-black text-white">123 Bulevar Alta Velocidad</p>
                        <p class="text-slate-300">San Salvador, El Salvador</p>
                    </div>
                </div>
                
                <!-- Lado Derecho: Mapa Embebido -->
                <div class="md:w-2/3 h-64 md:h-auto bg-slate-800 relative">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d124036.03541703058!2d-89.28472535097463!3d13.691478144216843!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f6330691522883d%3A0xc5cd1c2c2f735d4f!2sSan%20Salvador!5e0!3m2!1sen!2ssv!4v1714704000000!5m2!1sen!2ssv" 
                        class="absolute inset-0 w-full h-full border-0 grayscale invert opacity-80" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </section>

            <!-- RESERVAS CON CUBIBOOK -->
            <section class="bg-[#ffcc00] border-4 border-slate-900 rounded-3xl p-10 md:p-16 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] text-center space-y-6 relative overflow-hidden">
                <span class="material-symbols-outlined text-9xl absolute -right-10 -bottom-10 text-yellow-500 opacity-50 rotate-[-15deg]">event_seat</span>
                
                <div class="relative z-10 max-w-2xl mx-auto space-y-6">
                    <h2 class="text-4xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue']">
                        ASEGURA TU MESA
                    </h2>
                    <p class="text-base font-black text-slate-900">
                        ¿Planeas una reunión familiar, de amigos o de negocios? Estamos completamente integrados con CubiBook para garantizar tu mesa. Sin esperas ni filas. Solo llega y disfruta.
                    </p>
                    
                    <button class="bg-slate-950 hover:bg-slate-800 text-white px-8 py-4 rounded-xl font-black text-xs uppercase tracking-widest border-2 border-slate-950 shadow-[4px_4px_0px_0px_rgba(183,16,42,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all inline-flex items-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined">book_online</span>
                        Reservar mesa vía CubiBook
                    </button>
                </div>
            </section>

            <!-- CONTACTO DIRECTO -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <!-- Columna Izquierda: Formulario -->
                <div class="bg-white border-2 border-slate-900 rounded-3xl p-10 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <h3 class="text-2xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] mb-6">ESCRIBENOS</h3>
                    
                    <form class="space-y-4" @submit.prevent>
                        <div>
                            <input type="text" placeholder="Tu Nombre" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold bg-white text-slate-900">
                        </div>
                        <div>
                            <input type="email" placeholder="Tu Correo Electrónico" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold bg-white text-slate-900">
                        </div>
                        <div>
                            <textarea placeholder="Cuéntanos sobre tu experiencia QuickBite..." rows="4" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold bg-white text-slate-900 resize-none"></textarea>
                        </div>
                        <button class="w-full bg-red-700 hover:bg-red-800 text-white px-6 py-4 rounded-xl font-black text-xs uppercase tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all">
                            ENVIAR MENSAJE
                        </button>
                    </form>
                </div>

                <!-- Columna Derecha: Información de Contacto -->
                <div class="space-y-8 flex flex-col justify-center">
                    <div>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Línea Directa</h3>
                        <p class="text-3xl font-black font-['Epilogue'] text-red-700">+503 7577-2377</p>
                        <p class="font-black text-slate-600 mt-2">hola@quickbite.express</p>
                    </div>
                    
                    <div>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Nuestras Redes</h3>
                        <div class="flex gap-4">
                            <a href="#" class="w-12 h-12 bg-white border-2 border-slate-900 rounded-full flex items-center justify-center text-slate-900 hover:bg-[#ffcc00] hover:scale-110 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] font-black text-sm">IG</a>
                            <a href="#" class="w-12 h-12 bg-white border-2 border-slate-900 rounded-full flex items-center justify-center text-slate-900 hover:bg-[#ffcc00] hover:scale-110 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] font-black text-sm">FB</a>
                            <a href="#" class="w-12 h-12 bg-white border-2 border-slate-900 rounded-full flex items-center justify-center text-slate-900 hover:bg-[#ffcc00] hover:scale-110 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] font-black text-sm">X</a>
                        </div>
                    </div>
                </div>
            </section>
            
        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
