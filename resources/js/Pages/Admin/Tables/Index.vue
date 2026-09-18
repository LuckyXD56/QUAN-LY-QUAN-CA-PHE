<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import QrcodeVue from 'qrcode.vue';
import { ref } from 'vue';

const props = defineProps({
    tables: Array,
});

const form = useForm({
    name: '',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.tables.store'), {
        onSuccess: () => form.reset(),
    });
};

const deleteTable = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa bàn này?')) {
        useForm({}).delete(route('admin.tables.destroy', id));
    }
};

const regenerateQr = (id) => {
    if (confirm('Tạo lại mã QR sẽ làm mã cũ không thể sử dụng. Tiếp tục?')) {
        useForm({}).post(route('admin.tables.regenerate-qr', id));
    }
}

const showQrModal = ref(false);
const selectedTable = ref(null);
const fullQrUrl = ref('');

const openQrModal = (tbl) => {
    selectedTable.value = tbl;
    fullQrUrl.value = route('customer.menu', tbl.qr_token);
    showQrModal.value = true;
};

const closeQrModal = () => {
    showQrModal.value = false;
    selectedTable.value = null;
};

const printQr = () => {
    window.print();
};
</script>

<template>
    <Head title="Quản lý Bàn" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Quản lý Bàn & QR Code
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Add Table Form -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 print:hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thêm Bàn Mới</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Tên bàn (vd: Bàn 1, Bàn VIP)" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                        </div>
                        <div>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Thêm Bàn
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Table List -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 print:hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Danh Sách Bàn</h3>
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th class="px-6 py-3">ID</th>
                                <th class="px-6 py-3">Tên bàn</th>
                                <th class="px-6 py-3">Mã QR (Token)</th>
                                <th class="px-6 py-3">Trạng thái</th>
                                <th class="px-6 py-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="tbl in tables" :key="tbl.id" class="border-b bg-white">
                                <td class="px-6 py-4">{{ tbl.id }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ tbl.name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono bg-gray-100 p-1 rounded text-xs truncate max-w-[100px] block">{{ tbl.qr_token }}</span>
                                        <button @click="openQrModal(tbl)" class="text-indigo-600 font-bold hover:underline bg-indigo-50 px-2 py-1 rounded">📱 Xem QR</button>
                                        <button @click="regenerateQr(tbl.id)" class="text-red-500 hover:underline text-xs">Tạo lại</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ tbl.is_active ? 'Hoạt động' : 'Đóng' }}</td>
                                <td class="px-6 py-4">
                                    <button @click="deleteTable(tbl.id)" class="text-red-600 hover:underline">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <Modal :show="showQrModal" @close="closeQrModal" maxWidth="sm">
            <div class="p-6 text-center">
                <h2 class="text-2xl font-bold mb-2">QR Code Đặt Món</h2>
                <p class="text-gray-500 mb-6">Quét mã này để gọi món tại {{ selectedTable?.name }}</p>
                
                <div class="flex justify-center bg-white p-4 rounded-xl shadow-inner inline-block mx-auto mb-6">
                    <qrcode-vue :value="fullQrUrl" :size="200" level="M" />
                </div>
                
                <p class="text-xs text-gray-400 mb-6 font-mono break-all">{{ fullQrUrl }}</p>

                <div class="flex justify-center space-x-4 print:hidden">
                    <button @click="closeQrModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded font-bold hover:bg-gray-300">Đóng</button>
                    <button @click="printQr" class="px-4 py-2 bg-indigo-600 text-white rounded font-bold hover:bg-indigo-700">🖨️ In Mã QR</button>
                </div>
            </div>
        </Modal>

        <!-- Printable QR Section (Only visible when printing) -->
        <div class="hidden print:flex fixed inset-0 z-[9999] bg-white items-center justify-center" v-if="selectedTable">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Mời Quý Khách Quét Mã</h1>
                <h2 class="text-2xl mb-8">Để xem Menu và Gọi món tại {{ selectedTable.name }}</h2>
                <div class="flex justify-center">
                    <qrcode-vue :value="fullQrUrl" :size="400" level="M" />
                </div>
                <p class="mt-8 text-gray-500 text-sm">Cảm ơn quý khách!</p>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print\:flex, .print\:flex * {
        visibility: visible;
    }
    .print\:flex {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }
}
</style>
