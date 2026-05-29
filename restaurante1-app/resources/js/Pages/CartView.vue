<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import { useCart } from '@/Composables/useCart';

// Extraemos estado y métodos del carrito global
const { cartItems, updateQuantity, removeFromCart, clearCart, cartSubtotal, cartCount } = useCart();

const page = usePage();

// Datos del formulario de envío
const deliveryForm = ref({
    fullName: page.props.auth?.user?.name || '',
    phone: '',
    address: '',
    instructions: ''
});

// Tarifas calculadas
const deliveryFee = 2.50;
const processingFee = 0.99;

// Formateador de moneda profesional
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

// Total general
const orderTotal = computed(() => {
    if (cartCount.value === 0) return 0;
    return cartSubtotal.value + deliveryFee + processingFee;
});

// Mensajes de error del servidor
const serverError = ref('');
const isSubmitting = ref(false);

// Confirmar pedido y guardarlo en la base de datos de Laravel
const confirmOrderInDatabase = () => {
    if (cartCount.value === 0) {
        alert("El carrito está vacío.");
        return;
    }

    if (!deliveryForm.value.fullName || !deliveryForm.value.phone || !deliveryForm.value.address) {
        alert("Por favor completa los campos de Nombre, Teléfono y Dirección de Entrega.");
        return;
    }

    // Verificar si el usuario está autenticado
    if (!page.props.auth?.user) {
        alert("Debes iniciar sesión para poder confirmar tu pedido.");
        router.visit(route('login'));
        return;
    }

    isSubmitting.value = true;
    serverError.value = '';

    const payload = {
        items: cartItems.value.map(item => ({
            product_id: item.product.id,
            quantity: item.quantity
        })),
        total: orderTotal.value,
        notes: `Cliente: ${deliveryForm.value.fullName} | Teléfono: ${deliveryForm.value.phone} | Dirección: ${deliveryForm.value.address} | Instrucciones: ${deliveryForm.value.instructions || 'Ninguna'}`
    };

    router.post(route('orders.store'), payload, {
        onSuccess: () => {
            clearCart();
            isSubmitting.value = false;
        },
        onError: (errors) => {
            isSubmitting.value = false;
            serverError.value = errors.error || 'Hubo un error al procesar el pedido. Por favor intenta de nuevo.';
        }
    });
};

// Vaciar carrito completo
const handleClearCart = () => {
    if (confirm("¿Estás seguro de que quieres vaciar todo el carrito?")) {
        clearCart();
    }
};

</script>

<template>
    <Head title="Carrito de Compras | QuickBite Express" />
    
    <div class="bg-slate-50 text-slate-900 min-h-screen font-['Be_Vietnam_Pro'] pb-20">
        
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-6xl mx-auto px-6 pt-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 border-b-2 border-slate-200 pb-6">
                <div>
                    <h1 class="text-4xl md:text-5xl font-black text-red-700 tracking-tighter font-['Epilogue'] uppercase italic leading-none">
                        TU CARRITO
                    </h1>
                    <p class="text-slate-500 font-bold mt-2">Revisa tus productos y completa tu orden.</p>
                </div>
                <button 
                    v-if="cartCount > 0"
                    @click="handleClearCart"
                    class="bg-white text-red-700 border-2 border-red-700 hover:bg-red-50 px-4 py-2 rounded-xl font-black text-xs uppercase tracking-widest transition-all w-fit"
                >
                    VACIAR CARRITO
                </button>
            </div>

            <!-- Mostrar error de servidor si existe -->
            <div v-if="serverError" class="mb-6 p-4 bg-red-100 border-2 border-red-300 rounded-2xl text-red-800 font-bold text-sm">
                {{ serverError }}
            </div>

            <!-- Grid a dos columnas para escritorio -->
            <div class="flex flex-col lg:flex-row gap-10 items-start">
                
                <!-- COLUMNA IZQUIERDA (Items y Formulario) -->
                <div class="flex-grow w-full space-y-10">
                    
                    <!-- SECCIÓN: REVIEW ITEMS -->
                    <section>
                        <h2 class="text-xl font-black mb-6 flex items-center gap-2 text-slate-900 font-['Epilogue'] uppercase italic tracking-tight">
                            <span class="material-symbols-outlined text-red-700">shopping_bag</span>
                            PRODUCTOS SELECCIONADOS
                        </h2>

                        <div v-if="cartCount === 0" class="bg-white border-2 border-slate-900 rounded-2xl p-12 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                            <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 animate-bounce">production_quantity_limits</span>
                            <p class="text-slate-500 font-black text-xl">¡Tu carrito está vacío!</p>
                            <p class="text-sm text-slate-400 mt-2">Agrega deliciosos productos desde el menú.</p>
                            <Link :href="route('public.menu')" class="inline-block mt-6 bg-[#ffcc00] border-2 border-slate-900 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest text-slate-950 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all">Ver Menú</Link>
                        </div>

                        <!-- Lista de productos -->
                        <div v-else class="space-y-4">
                            <div v-for="item in cartItems" :key="item.product.id" class="bg-white border-2 border-slate-900 rounded-2xl p-4 flex gap-4 md:gap-6 items-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                
                                <!-- Imagen del producto -->
                                <div class="w-20 h-20 md:w-24 md:h-24 bg-slate-100 rounded-xl overflow-hidden border-2 border-slate-900 shrink-0">
                                    <img :src="item.product.image || item.product.image_path" :alt="item.product.name" class="w-full h-full object-cover">
                                </div>

                                <!-- Detalles del producto -->
                                <div class="flex-grow">
                                    <h3 class="font-black text-lg leading-tight text-slate-900 font-['Epilogue']">{{ item.product.name }}</h3>
                                    <p class="text-xs font-bold text-red-700 mt-1 uppercase tracking-wider">
                                        {{ typeof item.product.category === 'object' ? item.product.category?.name : item.product.category }}
                                    </p>
                                    
                                    <!-- Controles de cantidad y eliminar -->
                                    <div class="flex items-center gap-4 mt-3">
                                        <!-- Selector de cantidad -->
                                        <div class="flex items-center border-2 border-slate-900 rounded-lg overflow-hidden bg-[#ffcc00] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <button @click="updateQuantity(item.product.id, -1)" class="px-3 py-1 hover:bg-yellow-500 transition-colors font-black text-slate-900 cursor-pointer">-</button>
                                            <span class="px-3 py-1 bg-white border-x-2 border-slate-900 font-bold text-sm min-w-[2.5rem] text-center select-none text-slate-900">{{ item.quantity }}</span>
                                            <button @click="updateQuantity(item.product.id, 1)" class="px-3 py-1 hover:bg-yellow-500 transition-colors font-black text-slate-900 cursor-pointer">+</button>
                                        </div>

                                        <!-- Botón Eliminar -->
                                        <button @click="removeFromCart(item.product.id)" class="text-red-700 text-xs font-black uppercase tracking-widest hover:underline flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[14px]">delete</span> Eliminar
                                        </button>
                                    </div>
                                </div>

                                <!-- Precio -->
                                <div class="text-right">
                                    <div class="font-black text-lg text-slate-900 font-['Epilogue']">
                                        {{ formatPrice(item.product.price * item.quantity) }}
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400">
                                        {{ formatPrice(item.product.price) }} c/u
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN: DELIVERY DETAILS -->
                    <section v-if="cartCount > 0">
                        <h2 class="text-xl font-black mb-6 flex items-center gap-2 text-slate-900 font-['Epilogue'] uppercase italic tracking-tight">
                            <span class="material-symbols-outlined text-red-700">local_shipping</span>
                            DATOS DE ENTREGA Y ENVÍO
                        </h2>

                        <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">Nombre de quien Recibe *</label>
                                    <input v-model="deliveryForm.fullName" required type="text" placeholder="Ej. Juan Pérez" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold bg-white text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">Número de Teléfono *</label>
                                    <input v-model="deliveryForm.phone" required type="tel" placeholder="Ej. +503 7000-0000" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold bg-white text-slate-900">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">Dirección de Entrega *</label>
                                <input v-model="deliveryForm.address" required type="text" placeholder="Calle, Colonia, N° de casa, Referencias" class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold bg-white text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-700 mb-2">Instrucciones Especiales (Opcional)</label>
                                <textarea v-model="deliveryForm.instructions" rows="3" placeholder="Ej. Entregar en la caseta de vigilancia, tocar timbre portón negro, etc." class="w-full rounded-xl border-2 border-slate-900 focus:border-red-700 focus:ring-0 transition-colors px-4 py-3 text-sm font-bold bg-white text-slate-900 resize-none"></textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- COLUMNA DERECHA (Resumen y Botón Confirmar) -->
                <aside v-if="cartCount > 0" class="w-full lg:w-96 shrink-0 space-y-6">
                    
                    <!-- Resumen del pedido -->
                    <div class="bg-slate-900 text-white rounded-3xl p-8 shadow-[8px_8px_0px_0px_rgba(239,68,68,0.2)] border-2 border-slate-950">
                        <h2 class="text-2xl font-black mb-6 font-['Epilogue'] tracking-tight uppercase italic text-yellow-400">Resumen de Orden</h2>
                        
                        <div class="space-y-4 mb-6 text-sm text-slate-300 font-bold uppercase tracking-wider">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="text-white">{{ formatPrice(cartSubtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Costo de Envío</span>
                                <span class="text-white">{{ formatPrice(deliveryFee) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Servicio (QuickBite)</span>
                                <span class="text-white">{{ formatPrice(processingFee) }}</span>
                            </div>
                        </div>

                        <div class="border-t-2 border-slate-700 pt-6 mb-8 flex justify-between items-center">
                            <span class="text-xl font-black uppercase italic">TOTAL</span>
                            <span class="text-3xl font-black text-[#ffcc00] font-['Epilogue']">{{ formatPrice(orderTotal) }}</span>
                        </div>

                        <!-- Botón Base de Datos (Si está logueado) -->
                        <div v-if="page.props.auth?.user" class="space-y-4">
                            <button 
                                @click="confirmOrderInDatabase"
                                :disabled="isSubmitting"
                                class="w-full bg-[#ffcc00] hover:bg-yellow-500 text-slate-950 font-black py-4.5 rounded-xl flex items-center justify-center gap-2 transition-all font-['Epilogue'] uppercase text-sm shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] hover:shadow-none hover:translate-y-1 hover:translate-x-1 cursor-pointer border-2 border-slate-950 disabled:opacity-50"
                            >
                                <span class="material-symbols-outlined text-[18px]">shopping_cart_checkout</span>
                                {{ isSubmitting ? 'PROCESANDO...' : 'CONFIRMAR PEDIDO' }}
                            </button>
                        </div>

                        <!-- Botón para forzar inicio de sesión -->
                        <div v-else class="space-y-4">
                            <Link 
                                :href="route('login')"
                                class="w-full bg-[#ffcc00] hover:bg-yellow-500 text-slate-950 font-black py-4 rounded-xl flex items-center justify-center gap-2 transition-all font-['Epilogue'] uppercase text-xs text-center border-2 border-slate-950 shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] hover:shadow-none hover:translate-y-1 hover:translate-x-1"
                            >
                                <span class="material-symbols-outlined text-[16px]">login</span>
                                INICIA SESIÓN PARA PEDIR
                            </Link>
                            <p class="text-[10px] text-center text-slate-400 font-bold uppercase tracking-wider">
                                Debes estar registrado para guardar tus pedidos en el sistema.
                            </p>
                        </div>

                        <!-- Botón Volver al menú -->
                        <Link :href="route('public.menu')" class="w-full bg-white text-slate-900 hover:bg-slate-100 font-black py-4.5 rounded-xl flex items-center justify-center transition-all text-xs uppercase text-center mt-4 border-2 border-slate-950 shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] hover:shadow-none hover:translate-y-1 hover:translate-x-1">
                            SEGUIR COMPRANDO
                        </Link>

                        <p class="text-center text-[10px] text-slate-400 mt-6 px-4 font-semibold">
                            Tu pedido llegará en un promedio de 20 minutos con nuestro servicio Express de alta velocidad.
                        </p>
                    </div>

                    <!-- Código Promocional Banner -->
                    <div class="bg-[#ffcc00] border-2 border-slate-900 rounded-2xl p-4 flex items-center justify-between cursor-pointer hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-950 font-bold">local_offer</span>
                            <div>
                                <p class="text-sm font-black text-slate-900 leading-tight">¿Tienes un cupón?</p>
                                <p class="text-xs text-slate-800 font-bold">Ingrésalo en la siguiente pantalla</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined font-black group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </div>

                </aside>

            </div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.2s ease-out forwards;
}
</style>
