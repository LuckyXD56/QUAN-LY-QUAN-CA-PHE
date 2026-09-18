<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    table: Object,
    categories: Array,
    session_token: String,
    session_id: Number,
    placedOrders: Array
});

const { t, locale } = useI18n();

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem('locale', lang);
};

const cart = ref([]);
const cartModalOpen = ref(false);
const activeTab = ref('menu'); // 'menu' or 'tracking'
const liveOrders = ref(props.placedOrders || []);

onMounted(() => {
    if (window.Echo && props.session_id) {
        window.Echo.channel('session.' + props.session_id)
            .listen('.order.item.updated', (e) => {
                const updatedItem = e.item;
                const index = liveOrders.value.findIndex(item => item.id === updatedItem.id);
                if (index !== -1) {
                    liveOrders.value[index] = updatedItem;
                } else {
                    liveOrders.value.unshift(updatedItem);
                }
            });
    }
});

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
            // Force reload page to get updated placedOrders, or just let them wait
            window.location.reload(); 
        }
    });
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-gray-100 text-gray-800',
        'cooking': 'bg-orange-100 text-orange-800',
        'ready': 'bg-green-100 text-green-800',
        'served': 'bg-indigo-100 text-indigo-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Chờ xác nhận',
        'cooking': 'Đang nấu',
        'ready': 'Đã xong',
        'served': 'Đã phục vụ',
        'cancelled': 'Đã hủy'
    };
    return labels[status] || status;
};
</script>

<template>
    <Head :title="`${t('menu')} - ${table.name}`" />

    <div class="min-h-screen bg-gray-50 pb-24">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="max-w-3xl mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-900">{{ t('menu') }} - {{ table.name }}</h1>
                
                <!-- Language Switcher -->
                <div class="flex space-x-2">
                    <button @click="changeLanguage('th')" :class="locale === 'th' ? 'font-bold underline text-indigo-600' : 'text-gray-500'">TH</button>
                    <button @click="changeLanguage('vi')" :class="locale === 'vi' ? 'font-bold underline text-indigo-600' : 'text-gray-500'">VN</button>
                    <button @click="changeLanguage('en')" :class="locale === 'en' ? 'font-bold underline text-indigo-600' : 'text-gray-500'">EN</button>
                    <button @click="changeLanguage('lo')" :class="locale === 'lo' ? 'font-bold underline text-indigo-600' : 'text-gray-500'">LA</button>
                </div>
            </div>
            
            <div class="max-w-3xl mx-auto px-4 flex border-t">
                <button @click="activeTab = 'menu'" :class="activeTab === 'menu' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="flex-1 py-3 border-b-2 font-medium text-center transition">Thực đơn</button>
                <button @click="activeTab = 'tracking'" :class="activeTab === 'tracking' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="flex-1 py-3 border-b-2 font-medium text-center transition">Món đã gọi</button>
            </div>
        </header>

        <!-- Menu Content -->
        <main v-show="activeTab === 'menu'" class="max-w-3xl mx-auto px-4 mt-6">
            <div v-for="category in categories" :key="category.id" class="mb-8">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">{{ category.name }}</h2>
                <div class="space-y-4">
                    <div v-for="product in category.products" :key="product.id" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <div v-if="product.image" class="w-16 h-16 rounded-lg overflow-hidden shrink-0 bg-gray-100">
                                <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ product.name }}</h3>
                                <div class="text-sm text-gray-500">{{ product.description }}</div>
                                <div class="text-indigo-600 font-bold mt-1">{{ parseFloat(product.price).toLocaleString() }} đ</div>
                            </div>
                        </div>
                        <button @click="addToCart(product)" class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-full text-sm font-semibold hover:bg-indigo-100 transition ring-1 ring-indigo-200 shrink-0 ml-4">
                            {{ t('add') }}
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Tracking Content -->
        <main v-show="activeTab === 'tracking'" class="max-w-3xl mx-auto px-4 mt-6">
            <div v-if="liveOrders.length === 0" class="text-center text-gray-500 mt-10">
                Chưa có món nào được gọi.
            </div>
            <div class="space-y-4">
                <div v-for="item in liveOrders" :key="item.id" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center ring-1 ring-gray-100">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ item.product?.name }} <span class="text-gray-500 text-sm">x{{ item.quantity }}</span></h3>
                        <div v-if="item.notes" class="text-xs text-gray-500">Ghi chú: {{ item.notes }}</div>
                    </div>
                    <span :class="getStatusColor(item.status)" class="px-3 py-1 rounded-full text-xs font-bold shadow-sm ring-1 ring-black/5">
                        {{ getStatusLabel(item.status) }}
                    </span>
                </div>
            </div>
        </main>

        <!-- Floating Cart Button -->
        <div v-if="cart.length > 0 && activeTab === 'menu'" class="fixed bottom-0 left-0 w-full p-4 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-20">
            <div class="max-w-3xl mx-auto flex justify-between items-center">
                <div>
                    <span class="text-gray-500 text-sm">{{ t('total') }}:</span>
                    <div class="text-xl font-bold text-indigo-600">{{ cartTotal.toLocaleString() }} đ</div>
                </div>
                <button @click="cartModalOpen = true" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:bg-indigo-700 flex items-center">
                    <span class="bg-white text-indigo-600 rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">{{ cartItemCount }}</span>
                    {{ t('view_cart') }}
                </button>
            </div>
        </div>

        <!-- Cart Modal -->
        <Modal :show="cartModalOpen" @close="cartModalOpen = false">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">{{ t('your_cart') }}</h2>
                
                <div class="max-h-96 overflow-y-auto pr-2">
                    <div v-for="item in cart" :key="item.id" class="flex justify-between items-center mb-4 border-b pb-4">
                        <div class="flex-1">
                            <div class="font-medium">{{ item.name }}</div>
                            <div class="text-sm text-gray-500">{{ parseFloat(item.price).toLocaleString() }} đ</div>
                            <input type="text" v-model="item.notes" :placeholder="t('notes_placeholder')" class="mt-1 text-sm border-gray-300 rounded w-full">
                        </div>
                        <div class="flex items-center ml-4">
                            <button @click="decreaseQty(item)" class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-bold flex items-center justify-center">-</button>
                            <span class="mx-3 font-medium">{{ item.quantity }}</span>
                            <button @click="increaseQty(item)" class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center">+</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('general_notes') }}</label>
                    <textarea v-model="form.notes" rows="2" class="w-full border-gray-300 rounded-md shadow-sm" :placeholder="t('general_notes_placeholder')"></textarea>
                </div>

                <div class="mt-6 flex justify-between items-center">
                    <div class="text-xl font-bold text-gray-900">{{ t('total') }}: {{ cartTotal.toLocaleString() }} đ</div>
                    <div class="space-x-3">
                        <button @click="cartModalOpen = false" class="text-gray-500 font-medium">{{ t('close') }}</button>
                        <button @click="submitOrder" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 shadow-sm">
                            {{ t('submit_order') }}
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>
