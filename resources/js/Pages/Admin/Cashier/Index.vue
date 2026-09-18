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

const printBill = () => {
    window.print();
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

                <div class="mt-6 flex justify-between">
                    <button type="button" @click="printBill" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        🖨️ In tạm tính
                    </button>
                    <div class="flex">
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
            </div>
        </Modal>

        <!-- Printable Bill Area (Hidden on screen, visible on print) -->
        <div id="print-area" class="hidden print:block fixed top-0 left-0 w-full h-full bg-white z-[9999] p-4 text-black font-mono text-sm" v-if="selectedSession">
            <div class="max-w-[80mm] mx-auto border-b-2 border-dashed pb-4 mb-4">
                <h1 class="text-xl font-bold text-center mb-1">COFFEE SHOP POS</h1>
                <p class="text-center text-xs mb-4">Hóa đơn tạm tính</p>
                <div class="flex justify-between mb-1">
                    <span>Bàn: {{ tables.find(t => t.active_session?.id === selectedSession.id)?.name }}</span>
                    <span>Ngày: {{ new Date().toLocaleDateString('vi-VN') }}</span>
                </div>
            </div>
            
            <div class="max-w-[80mm] mx-auto">
                <table class="w-full text-left mb-4">
                    <thead>
                        <tr class="border-b">
                            <th class="py-1">Món</th>
                            <th class="py-1 text-right">SL</th>
                            <th class="py-1 text-right">Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="order in selectedSession.orders" :key="order.id">
                            <tr v-for="item in order.items" :key="item.id" v-show="item.status !== 'cancelled'">
                                <td class="py-1 pr-2">{{ item.product.name }}</td>
                                <td class="py-1 text-right">{{ item.quantity }}</td>
                                <td class="py-1 text-right">{{ (item.price * item.quantity).toLocaleString() }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div class="border-t-2 border-dashed pt-2">
                    <div class="flex justify-between">
                        <span>Tổng tiền:</span>
                        <span class="font-bold">{{ calculateTotal(selectedSession).toLocaleString() }} đ</span>
                    </div>
                    <div class="flex justify-between" v-if="checkoutForm.discount > 0">
                        <span>Giảm giá:</span>
                        <span>-{{ checkoutForm.discount.toLocaleString() }} đ</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold mt-2 border-t pt-2">
                        <span>Thành tiền:</span>
                        <span>{{ finalAmount.toLocaleString() }} đ</span>
                    </div>
                </div>
                <p class="text-center text-xs mt-6">Xin cảm ơn và hẹn gặp lại!</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
