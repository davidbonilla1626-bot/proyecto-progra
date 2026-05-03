<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import { useCart } from '@/Composables/useCart';

// Extraemos estado y métodos del carrito global
const { cartItems, updateQuantity, removeFromCart, cartSubtotal, cartCount } = useCart();

// Datos del formulario de envío
const deliveryForm = ref({
    fullName: '',
    phone: '',
    address: '',
    instructions: ''
});

// Tarifas calculadas
const deliveryFee = 2.50;
const processingFee = 0.99;

// Formateador de moneda
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
};

// Total general
const orderTotal = computed(() => {
    if (cartCount.value === 0) return 0;
    return cartSubtotal.value + deliveryFee + processingFee;
});

// Función para enviar por WhatsApp
const sendOrderViaWhatsApp = () => {
    // Validar formulario básico
    if (!deliveryForm.value.fullName || !deliveryForm.value.phone || !deliveryForm.value.address) {
        alert("Please fill in your Full Name, Phone Number, and Delivery Address.");
        return;
    }

    if (cartCount.value === 0) {
        alert("Your bag is empty!");
        return;
    }

    // Número de negocio (Cámbialo por el tuyo)
    const whatsappNumber = "+50375772377";

    // Construir mensaje
    let message = `*NEW ORDER - QUICKBITE EXPRESS* 🍔\n\n`;
    message += `*Customer:* ${deliveryForm.value.fullName}\n`;
    message += `*Phone:* ${deliveryForm.value.phone}\n`;
    message += `*Address:* ${deliveryForm.value.address}\n\n`;
    
    if (deliveryForm.value.instructions) {
        message += `*Notes:* ${deliveryForm.value.instructions}\n\n`;
    }

    message += `*ORDER ITEMS:*\n`;
    cartItems.value.forEach(item => {
        message += `- ${item.quantity}x ${item.product.name} (${formatPrice(item.product.price)})\n`;
    });

    message += `\n*Subtotal:* ${formatPrice(cartSubtotal.value)}\n`;
    message += `*Delivery Fee:* ${formatPrice(deliveryFee)}\n`;
    message += `*Processing:* ${formatPrice(processingFee)}\n`;
    message += `*TOTAL:* ${formatPrice(orderTotal.value)}\n`;

    // Codificar URL y abrir WhatsApp
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
    
    window.open(whatsappUrl, '_blank');
};

</script>

<template>
    <Head title="QuickBite Express | Checkout" />
    
    <div class="bg-slate-50 text-slate-900 min-h-screen font-['Be_Vietnam_Pro'] pb-20">
        
        <!-- HEADER PÚBLICO -->
        <PublicHeader />

        <div class="max-w-6xl mx-auto px-6 pt-10">
            <h1 class="text-4xl md:text-5xl font-black text-red-700 tracking-tighter mb-10 font-['Epilogue']">
                Your Order Bag
            </h1>

            <!-- Grid a dos columnas para escritorio -->
            <div class="flex flex-col lg:flex-row gap-10 items-start">
                
                <!-- COLUMNA IZQUIERDA (Items y Formulario) -->
                <div class="flex-1 w-full space-y-10">
                    
                    <!-- SECCIÓN: REVIEW ITEMS -->
                    <section>
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-slate-900 font-['Epilogue']">
                            <span class="material-symbols-outlined">shopping_bag</span>
                            Review Items
                        </h2>

                        <div v-if="cartCount === 0" class="bg-white border-2 border-slate-200 rounded-2xl p-10 text-center">
                            <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">production_quantity_limits</span>
                            <p class="text-slate-500 font-bold text-lg">Your bag is empty!</p>
                            <Link :href="route('public.menu')" class="inline-block mt-4 text-red-700 font-bold underline hover:text-red-800">Browse Menu</Link>
                        </div>

                        <!-- Lista de productos -->
                        <div v-else class="space-y-4">
                            <div v-for="item in cartItems" :key="item.product.id" class="bg-white border-2 border-slate-900 rounded-2xl p-4 flex gap-4 md:gap-6 items-center shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)]">
                                
                                <!-- Imagen del producto -->
                                <div class="w-20 h-20 md:w-24 md:h-24 bg-slate-100 rounded-xl overflow-hidden border-2 border-slate-900 shrink-0">
                                    <img :src="item.product.image" :alt="item.product.name" class="w-full h-full object-cover">
                                </div>

                                <!-- Detalles del producto -->
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg leading-tight text-slate-900">{{ item.product.name }}</h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                        {{ typeof item.product.category === 'object' ? item.product.category?.name : item.product.category }} details included
                                    </p>
                                    
                                    <!-- Controles de cantidad y eliminar -->
                                    <div class="flex items-center gap-4 mt-3">
                                        <!-- Selector de cantidad -->
                                        <div class="flex items-center border-2 border-slate-900 rounded-lg overflow-hidden bg-yellow-400 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <button @click="updateQuantity(item.product.id, -1)" class="px-3 hover:bg-yellow-500 transition-colors font-black text-slate-900">-</button>
                                            <span class="px-3 py-1 bg-white border-x-2 border-slate-900 font-bold text-sm min-w-[2.5rem] text-center">{{ item.quantity }}</span>
                                            <button @click="updateQuantity(item.product.id, 1)" class="px-3 hover:bg-yellow-500 transition-colors font-black text-slate-900">+</button>
                                        </div>

                                        <!-- Botón Eliminar -->
                                        <button @click="removeFromCart(item.product.id)" class="text-red-700 text-xs font-bold hover:underline flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">delete</span> Remove
                                        </button>
                                    </div>
                                </div>

                                <!-- Precio -->
                                <div class="font-black text-lg text-red-700 self-start md:self-center font-['Epilogue']">
                                    {{ formatPrice(item.product.price) }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN: DELIVERY DETAILS -->
                    <section>
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-slate-900 font-['Epilogue']">
                            <span class="material-symbols-outlined">local_shipping</span>
                            Delivery Details
                        </h2>

                        <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)]">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Full Name</label>
                                    <input v-model="deliveryForm.fullName" type="text" placeholder="John Doe" class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Phone Number</label>
                                    <input v-model="deliveryForm.phone" type="tel" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm outline-none">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Delivery Address</label>
                                <input v-model="deliveryForm.address" type="text" placeholder="Street Name, Building No, Apartment" class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-600 mb-2">Special Instructions</label>
                                <textarea v-model="deliveryForm.instructions" rows="3" placeholder="e.g. Leave at the front gate, code is 1234..." class="w-full rounded-xl border-2 border-slate-300 focus:border-slate-900 focus:ring-0 transition-colors px-4 py-3 text-sm outline-none resize-none"></textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- COLUMNA DERECHA (Order Summary) -->
                <aside class="w-full lg:w-96 shrink-0 space-y-6">
                    
                    <!-- Resumen del pedido -->
                    <div class="bg-slate-900 text-white rounded-3xl p-8 shadow-[8px_8px_0px_0px_rgba(185,28,28,0.2)]">
                        <h2 class="text-2xl font-black mb-6 font-['Epilogue'] tracking-tight">Order Summary</h2>
                        
                        <div class="space-y-4 mb-6 text-sm text-slate-300 font-medium">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>{{ formatPrice(cartSubtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Delivery Fee</span>
                                <span>{{ formatPrice(cartCount > 0 ? deliveryFee : 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Processing</span>
                                <span>{{ formatPrice(cartCount > 0 ? processingFee : 0) }}</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-700 pt-6 mb-8 flex justify-between items-center">
                            <span class="text-xl font-bold">Total</span>
                            <span class="text-2xl font-black text-yellow-400 font-['Epilogue']">{{ formatPrice(orderTotal) }}</span>
                        </div>

                        <!-- Botón WhatsApp -->
                        <button 
                            @click="sendOrderViaWhatsApp"
                            :disabled="cartCount === 0"
                            class="w-full bg-[#25D366] hover:bg-[#1ebd5b] text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 transition-colors mb-4 disabled:opacity-50 disabled:cursor-not-allowed shadow-[4px_4px_0px_0px_rgba(0,0,0,0.3)] hover:shadow-none hover:translate-y-1 hover:translate-x-1"
                        >
                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="w-5 h-5 filter brightness-0 invert">
                            SEND ORDER VIA WHATSAPP
                        </button>

                        <!-- Botón Volver al menú -->
                        <Link :href="route('public.menu')" class="w-full bg-white text-slate-900 hover:bg-slate-200 font-black py-4 rounded-xl flex items-center justify-center transition-colors text-sm text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,0.3)] hover:shadow-none hover:translate-y-1 hover:translate-x-1">
                            CONTINUE ORDERING
                        </Link>

                        <p class="text-center text-[10px] text-slate-400 mt-6 px-4">
                            By placing an order, you agree to our High-Velocity Terms of Service.
                        </p>
                    </div>

                    <!-- Código Promocional Banner -->
                    <div class="bg-yellow-400 border-2 border-slate-900 rounded-2xl p-4 flex items-center justify-between cursor-pointer hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-900">local_offer</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900 leading-tight">Have a promo code?</p>
                                <p class="text-xs text-slate-800">Enter it for instant savings</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined font-bold group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </div>

                </aside>

            </div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Epilogue:wght@900&family=Be+Vietnam+Pro:wght@400;600;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
</style>
