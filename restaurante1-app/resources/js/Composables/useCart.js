import { ref, computed, watch } from 'vue';

// Estado global del carrito para que los datos se mantengan al cambiar de página
const cartItems = ref([]);

// Intentar cargar el carrito guardado previamente en el navegador (localStorage)
const savedCart = localStorage.getItem('quickbite_cart');
if (savedCart) {
    try {
        cartItems.value = JSON.parse(savedCart);
    } catch (e) {
        console.error('Error cargando el carrito desde localStorage', e);
    }
}

// Escuchar automáticamente cualquier cambio en cartItems y guardarlo en localStorage
watch(cartItems, (newVal) => {
    localStorage.setItem('quickbite_cart', JSON.stringify(newVal));
}, { deep: true });

export function useCart() {
    /**
     * Agrega un producto al carrito o incrementa su cantidad si ya existe
     * @param {Object} product El producto a agregar
     */
    const addToCart = (product) => {
        // Si el stock es 0, no permitir agregar
        if (product.stock <= 0) {
            alert(`El producto ${product.name} se encuentra agotado.`);
            return;
        }

        const existingItem = cartItems.value.find(item => item.product.id === product.id);
        const currentQty = existingItem ? existingItem.quantity : 0;
        
        if (currentQty + 1 > product.stock) {
            alert(`No puedes agregar más unidades de ${product.name}. Solo quedan ${product.stock} en inventario.`);
            return;
        }

        if (existingItem) {
            existingItem.quantity += 1; // Si ya existe, sumamos 1 a la cantidad
        } else {
            // Si no existe, lo agregamos como un nuevo elemento con cantidad 1
            cartItems.value.push({
                product: product,
                quantity: 1
            });
        }
    };

    /**
     * Elimina un producto completamente del carrito
     * @param {Number} productId ID del producto a eliminar
     */
    const removeFromCart = (productId) => {
        cartItems.value = cartItems.value.filter(item => item.product.id !== productId);
    };

    /**
     * Modifica la cantidad de un producto (sumar o restar)
     * @param {Number} productId ID del producto
     * @param {Number} amount Cantidad a sumar (ej: 1) o restar (ej: -1)
     */
    const updateQuantity = (productId, amount) => {
        const item = cartItems.value.find(i => i.product.id === productId);
        if (item) {
            if (amount > 0 && item.quantity + amount > item.product.stock) {
                alert(`No puedes pedir más de las unidades disponibles en inventario (${item.product.stock}).`);
                return;
            }
            item.quantity += amount;
            // Si la cantidad llega a 0 o menos, eliminamos el producto del carrito
            if (item.quantity <= 0) {
                removeFromCart(productId);
            }
        }
    };

    /**
     * Vacía todo el carrito
     */
    const clearCart = () => {
        cartItems.value = [];
    };

    /**
     * Calcula la cantidad total de artículos en el carrito
     */
    const cartCount = computed(() => {
        return cartItems.value.reduce((total, item) => total + item.quantity, 0);
    });

    /**
     * Calcula el subtotal (precio de cada producto multiplicado por su cantidad)
     */
    const cartSubtotal = computed(() => {
        return cartItems.value.reduce((total, item) => {
            // Asegurarse de que el precio sea un número
            const price = parseFloat(item.product.price);
            return total + (price * item.quantity);
        }, 0);
    });

    return {
        cartItems,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        cartCount,
        cartSubtotal
    };
}
