<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    tables: Array,
});

const checkoutModal = ref(false);
const selectedSession = ref(null);

const checkoutForm = useForm({
    payment_method: 'cash',
    discount: 0,
});

const calculateTotal = (session) => {
    let total = 0;
    if (session && session.orders) {
        session.orders.forEach(order => {
            order.items.forEach(item => {
                if (item.status !== 'cancelled') {
                    total += parseFloat(item.price) * parseInt(item.quantity);
                }
            });
        });
    }
    return total;
};

const finalAmount = computed(() => {
    const total = calculateTotal(selectedSession.value);
    return Math.max(0, total - checkoutForm.discount);
});

const openCheckout = (session) => {
    selectedSession.value = session;
    checkoutForm.discount = 0;
    checkoutForm.payment_method = 'cash';
    checkoutModal.value = true;
};

const closeCheckout = () => {
    checkoutModal.value = false;
    selectedSession.value = null;
};

const processCheckout = () => {
    checkoutForm.post(route('admin.cashier.checkout', selectedSession.value.id), {
        onSuccess: () => closeCheckout(),
    });
};
</script>

<template>
    <Head title="Thu ngân" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Thu Ngân (Cashier Dashboard)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div 
                        v-for="tbl in tables" 
                        :key="tbl.id" 
                        class="p-6 rounded-lg shadow-md text-center transition-transform hover:scale-105 cursor-pointer"
                        :class="tbl.active_session ? 'bg-orange-100 border-2 border-orange-500' : 'bg-green-100 border-2 border-green-500'"
                        @click="tbl.active_session ? openCheckout(tbl.active_session) : null"
                    >
                        <h3 class="text-lg font-bold text-gray-800">{{ tbl.name }}</h3>
                        <div class="mt-2 text-sm font-medium">
                            <span v-if="tbl.active_session" class="text-orange-700">Đang phục vụ</span>
                            <span v-else class="text-green-700">Bàn trống</span>
                        </div>
                        <div v-if="tbl.active_session" class="mt-2 text-lg font-bold text-gray-900">
                            {{ calculateTotal(tbl.active_session).toLocaleString() }} ₫
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="checkoutModal" @close="closeCheckout">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Thanh toán hóa đơn
                </h2>

                <div class="mt-4 max-h-60 overflow-y-auto" v-if="selectedSession">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="pb-2">Món</th>
                                <th class="pb-2 text-center">SL</th>
                                <th class="pb-2 text-right">Giá</th>
                                <th class="pb-2 text-right">Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="order in selectedSession.orders" :key="order.id">
                                <tr v-for="item in order.items" :key="item.id" class="border-b">
                                    <template v-if="item.status !== 'cancelled'">
                                        <td class="py-2">{{ item.product?.name }}</td>
                                        <td class="py-2 text-center">{{ item.quantity }}</td>
                                        <td class="py-2 text-right">{{ parseFloat(item.price).toLocaleString() }}</td>
                                        <td class="py-2 text-right">{{ (item.price * item.quantity).toLocaleString() }}</td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-gray-700">Tổng tiền món:</span>
                        <span class="font-bold text-lg">{{ calculateTotal(selectedSession).toLocaleString() }} ₫</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-medium text-gray-700">Giảm giá (₫):</label>
                        <input type="number" v-model="checkoutForm.discount" class="border-gray-300 rounded-md shadow-sm w-32 text-right">
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <label class="font-medium text-gray-700">Hình thức thanh toán:</label>
                        <select v-model="checkoutForm.payment_method" class="border-gray-300 rounded-md shadow-sm w-32">
                            <option value="cash">Tiền mặt</option>
                            <option value="transfer">Chuyển khoản</option>
                            <option value="card">Thẻ</option>
                        </select>
                    </div>
                    <div class="flex justify-between items-center border-t pt-2">
                        <span class="font-bold text-gray-900 text-xl">Thành tiền:</span>
                        <span class="font-bold text-red-600 text-2xl">{{ finalAmount.toLocaleString() }} ₫</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeCheckout">Hủy</SecondaryButton>
                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': checkoutForm.processing }"
                        :disabled="checkoutForm.processing"
                        @click="processCheckout"
                    >
                        Xác nhận Thanh toán
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
