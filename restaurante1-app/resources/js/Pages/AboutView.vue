<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
</script>

<template>
    <!-- Define el título de la pestaña del navegador -->
    <Head title="QuickBite Express | About Us" />

    <!-- CONTENEDOR PRINCIPAL -->
    <!-- Define el fondo claro, la tipografía base (Be Vietnam Pro) y un padding inferior -->
    <div class="bg-slate-50 min-h-screen font-['Be_Vietnam_Pro'] text-slate-900 pb-20">
        
        <!-- HEADER PÚBLICO -->
        <!-- Componente reutilizable que contiene la barra superior de navegación -->
        <PublicHeader />

        <!-- ÁREA CENTRAL DE CONTENIDO -->
        <!-- max-w-7xl centra el contenido y le da un ancho máximo. space-y-20 separa cada sección internamente -->
        <main class="max-w-7xl mx-auto px-6 mt-12 space-y-20">
            
            <!-- ============================================== -->
            <!-- SECCIÓN 1: HISTORIA Y MISIÓN                   -->
            <!-- ============================================== -->
            <!-- Utilizamos un grid de 2 columnas en pantallas medianas (md:grid-cols-2) -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                
                <!-- Columna Izquierda: Textos y Estadísticas -->
                <div class="space-y-6">
                    <!-- Título Principal con tipografía Epilogue en estilo 'black italic' para impacto visual -->
                    <h1 class="text-5xl md:text-6xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] leading-none">
                        BORN TO BE <br><span class="text-red-700">FAST.</span>
                    </h1>
                    
                    <!-- Párrafo descriptivo -->
                    <p class="text-lg text-slate-600 leading-relaxed font-medium">
                        QuickBite Express was founded in 2026 with a single, aggressive mission: to obliterate hunger at maximum velocity without compromising an ounce of quality. 
                        We believe that fast food shouldn't mean bad food. Our kitchens operate like well-oiled engines, pushing out high-octane flavors engineered for the modern world.
                    </p>
                    
                    <!-- Tarjetas de Estadísticas Rápidas (Flexbox para ponerlas lado a lado) -->
                    <div class="flex gap-4">
                        <!-- Tarjeta Amarilla: Hamburguesas servidas -->
                        <div class="bg-yellow-400 border-2 border-slate-900 rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center flex-1">
                            <p class="text-3xl font-black font-['Epilogue']">10K+</p>
                            <p class="text-xs font-black uppercase tracking-widest text-slate-700 mt-1">Burgers Flipped</p>
                        </div>
                        <!-- Tarjeta Roja: Tiempo de entrega -->
                        <div class="bg-red-700 border-2 border-slate-900 rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center flex-1 text-white">
                            <p class="text-3xl font-black font-['Epilogue']">&lt; 15m</p>
                            <p class="text-xs font-black uppercase tracking-widest text-red-200 mt-1">Avg. Delivery</p>
                        </div>
                    </div>
                </div>
                
                <!-- Columna Derecha: Imagen decorativa -->
                <!-- Contenedor con borde oscuro, sombra roja y efecto hover (la imagen hace zoom suavemente) -->
                <div class="h-[400px] border-4 border-slate-900 rounded-3xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(183,16,42,1)] bg-slate-200 relative group">
                    <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?w=800" alt="QuickBite Kitchen" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <!-- Capa roja superpuesta para dar estilo a la foto -->
                    <div class="absolute inset-0 bg-red-700 mix-blend-overlay opacity-50"></div>
                </div>
            </section>

            <!-- ============================================== -->
            <!-- SECCIÓN 2: HORARIOS Y UBICACIÓN                -->
            <!-- ============================================== -->
            <!-- Tarjeta grande oscura que se divide en dos: texto a la izquierda, mapa a la derecha -->
            <section class="bg-slate-900 text-white border-4 border-slate-900 rounded-3xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(255,204,0,1)] flex flex-col md:flex-row">
                
                <!-- Lado Izquierdo: Lista de Horarios -->
                <div class="p-10 md:p-12 md:w-1/3 space-y-8 flex flex-col justify-center border-b-4 md:border-b-0 md:border-r-4 border-slate-900">
                    <h2 class="text-3xl font-black italic uppercase tracking-tighter font-['Epilogue'] text-yellow-400">Hours & Location</h2>
                    
                    <!-- Lista de días y horas -->
                    <ul class="space-y-4 text-sm font-bold tracking-wide">
                        <li class="flex justify-between border-b border-slate-700 pb-2">
                            <span class="text-slate-400">MON - THU</span>
                            <span>11:00 AM - 10:00 PM</span>
                        </li>
                        <li class="flex justify-between border-b border-slate-700 pb-2">
                            <span class="text-slate-400">FRI - SAT</span>
                            <span class="text-yellow-400">11:00 AM - 02:00 AM</span>
                        </li>
                        <li class="flex justify-between border-b border-slate-700 pb-2">
                            <span class="text-slate-400">SUNDAY</span>
                            <span>12:00 PM - 09:00 PM</span>
                        </li>
                    </ul>

                    <!-- Dirección -->
                    <div>
                        <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-1">HQ Address</p>
                        <p class="font-bold">123 High-Velocity Blvd.</p>
                        <p class="font-bold">San Salvador, El Salvador</p>
                    </div>
                </div>
                
                <!-- Lado Derecho: Mapa Embebido -->
                <div class="md:w-2/3 h-64 md:h-auto bg-slate-800 relative">
                    <!-- Mapa de Google Maps (usamos grayscale e invert para que combine con el tema oscuro) -->
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d124036.03541703058!2d-89.28472535097463!3d13.691478144216843!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f6330691522883d%3A0xc5cd1c2c2f735d4f!2sSan%20Salvador!5e0!3m2!1sen!2ssv!4v1714704000000!5m2!1sen!2ssv" 
                        class="absolute inset-0 w-full h-full border-0 grayscale invert opacity-80" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </section>

            <!-- ============================================== -->
            <!-- SECCIÓN 3: RESERVAS CON CUBIBOOK               -->
            <!-- ============================================== -->
            <!-- Banner amarillo destacado promocionando el sistema de reservas -->
            <section class="bg-yellow-400 border-4 border-slate-900 rounded-3xl p-10 md:p-16 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] text-center space-y-6 relative overflow-hidden">
                <!-- Icono de silla gigante difuminado en el fondo como marca de agua -->
                <span class="material-symbols-outlined text-9xl absolute -right-10 -bottom-10 text-yellow-500 opacity-50 rotate-[-15deg]">event_seat</span>
                
                <div class="relative z-10 max-w-2xl mx-auto space-y-6">
                    <h2 class="text-4xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue']">
                        SECURE YOUR SPOT
                    </h2>
                    <p class="text-lg font-bold text-slate-800">
                        Planning a meetup? We are fully integrated with <strong>Cubibook</strong> to guarantee your table. No waiting, no friction. Just arrive and eat.
                    </p>
                    
                    <!-- Botón de acción hacia Cubibook -->
                    <button class="bg-slate-900 text-white px-8 py-4 rounded-xl font-black text-sm uppercase tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(183,16,42,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all inline-flex items-center gap-2">
                        <span class="material-symbols-outlined">book_online</span>
                        Reserve via Cubibook
                    </button>
                </div>
            </section>

            <!-- ============================================== -->
            <!-- SECCIÓN 4: CONTACTO DIRECTO                    -->
            <!-- ============================================== -->
            <!-- Formulario de contacto y enlaces a redes sociales divididos en 2 columnas -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <!-- Columna Izquierda: Formulario -->
                <div class="bg-white border-2 border-slate-900 rounded-3xl p-10 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)]">
                    <h3 class="text-2xl font-black italic uppercase tracking-tighter text-slate-900 font-['Epilogue'] mb-6">Drop us a line</h3>
                    
                    <form class="space-y-4" @submit.prevent>
                        <!-- Input Nombre -->
                        <div>
                            <input type="text" placeholder="Your Name" class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold outline-none placeholder:font-normal">
                        </div>
                        <!-- Input Correo -->
                        <div>
                            <input type="email" placeholder="Your Email" class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold outline-none placeholder:font-normal">
                        </div>
                        <!-- Textarea Mensaje -->
                        <div>
                            <textarea placeholder="Tell us how fast it was..." rows="4" class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold outline-none resize-none placeholder:font-normal"></textarea>
                        </div>
                        <!-- Botón de Envío -->
                        <button class="w-full bg-red-700 text-white px-6 py-4 rounded-xl font-black text-sm uppercase tracking-widest border-2 border-slate-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all">
                            SEND MESSAGE
                        </button>
                    </form>
                </div>

                <!-- Columna Derecha: Información de Contacto -->
                <div class="space-y-8 flex flex-col justify-center">
                    
                    <!-- Teléfono y Correo -->
                    <div>
                        <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Direct Contact</h3>
                        <p class="text-3xl font-black font-['Epilogue'] text-red-700">+503 7577-2377</p>
                        <p class="font-bold text-slate-600 mt-2">hello@quickbite.express</p>
                    </div>
                    
                    <!-- Redes Sociales (Círculos flotantes) -->
                    <div>
                        <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Social Network</h3>
                        <div class="flex gap-4">
                            <a href="#" class="w-12 h-12 bg-white border-2 border-slate-900 rounded-full flex items-center justify-center text-slate-900 hover:bg-yellow-400 hover:scale-110 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                <span class="font-black text-xl">ig</span>
                            </a>
                            <a href="#" class="w-12 h-12 bg-white border-2 border-slate-900 rounded-full flex items-center justify-center text-slate-900 hover:bg-yellow-400 hover:scale-110 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                <span class="font-black text-xl">fb</span>
                            </a>
                            <a href="#" class="w-12 h-12 bg-white border-2 border-slate-900 rounded-full flex items-center justify-center text-slate-900 hover:bg-yellow-400 hover:scale-110 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                <span class="font-black text-xl">x</span>
                            </a>
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
