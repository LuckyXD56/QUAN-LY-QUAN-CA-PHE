<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    table: Object,
    categories: Array,
    session_token: String
});

const cart = ref([]);
const cartModalOpen = ref(false);

const addToCart = (product) => {
    const existing = cart.value.find(item => item.id === product.id);
    if (existing) {
        existing.quantity++;
    } else {
        cart.value.push({ ...product, quantity: 1, notes: '' });
    }
};

const increaseQty = (item) => item.quantity++;
const decreaseQty = (item) => {
    if (item.quantity > 1) {
        item.quantity--;
    } else {
        cart.value = cart.value.filter(i => i.id !== item.id);
    }
};

const cartTotal = computed(() => {
    return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});
const cartItemCount = computed(() => {
    return cart.value.reduce((total, item) => total + item.quantity, 0);
});

const form = useForm({
    cart: [],
    notes: ''
});

const submitOrder = () => {
    form.cart = cart.value;
    form.post(route('customer.order', props.table.qr_token), {
        onSuccess: () => {
            cart.value = [];
            cartModalOpen.value = false;
            alert('Đã gửi món thành công! Vui lòng chờ trong giây lát.');
        }
    });
};
</script>

<template>
    <Head :title="`Menu - ${table.name}`" />

    <div class="min-h-screen bg-gray-50 pb-24">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="max-w-3xl mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-900">Menu - {{ table.name }}</h1>
            </div>
        </header>

        <!-- Menu Content -->
        <main class="max-w-3xl mx-auto px-4 mt-6">
            <div v-for="category in categories" :key="category.id" class="mb-8">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">{{ category.name }}</h2>
                <div class="space-y-4">
                    <div v-for="product in category.products" :key="product.id" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ product.name }}</h3>
                            <div class="text-sm text-gray-500">{{ product.description }}</div>
                            <div class="text-indigo-600 font-bold mt-1">{{ parseFloat(product.price).toLocaleString() }} ₫</div>
                        </div>
                        <button @click="addToCart(product)" class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-full text-sm font-semibold hover:bg-indigo-100 transition">
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Floating Cart Button -->
        <div v-if="cart.length > 0" class="fixed bottom-0 left-0 w-full p-4 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-20">
            <div class="max-w-3xl mx-auto flex justify-between items-center">
                <div>
                    <span class="text-gray-500 text-sm">Tổng cộng:</span>
                    <div class="text-xl font-bold text-indigo-600">{{ cartTotal.toLocaleString() }} ₫</div>
                </div>
                <button @click="cartModalOpen = true" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:bg-indigo-700 flex items-center">
                    <span class="bg-white text-indigo-600 rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">{{ cartItemCount }}</span>
                    Xem giỏ hàng
                </button>
            </div>
        </div>

        <!-- Cart Modal -->
        <Modal :show="cartModalOpen" @close="cartModalOpen = false">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Giỏ hàng của bạn</h2>
                
                <div class="max-h-96 overflow-y-auto pr-2">
                    <div v-for="item in cart" :key="item.id" class="flex justify-between items-center mb-4 border-b pb-4">
                        <div class="flex-1">
                            <div class="font-medium">{{ item.name }}</div>
                            <div class="text-sm text-gray-500">{{ parseFloat(item.price).toLocaleString() }} ₫</div>
                            <input type="text" v-model="item.notes" placeholder="Ghi chú (vd: Ít đá, không cay...)" class="mt-1 text-sm border-gray-300 rounded w-full">
                        </div>
                        <div class="flex items-center ml-4">
                            <button @click="decreaseQty(item)" class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-bold flex items-center justify-center">-</button>
                            <span class="mx-3 font-medium">{{ item.quantity }}</span>
                            <button @click="increaseQty(item)" class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center">+</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú chung cho bàn:</label>
                    <textarea v-model="form.notes" rows="2" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="VD: Xin thêm 2 ly đá..."></textarea>
                </div>

                <div class="mt-6 flex justify-between items-center">
                    <div class="text-xl font-bold text-gray-900">Tổng: {{ cartTotal.toLocaleString() }} ₫</div>
                    <div class="space-x-3">
                        <button @click="cartModalOpen = false" class="text-gray-500 font-medium">Đóng</button>
                        <button @click="submitOrder" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">
                            Gửi món
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>
