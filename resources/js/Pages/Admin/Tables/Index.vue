<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

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
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
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
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
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
                                    <span class="font-mono bg-gray-100 p-1 rounded">{{ tbl.qr_token }}</span>
                                    <button @click="regenerateQr(tbl.id)" class="ml-2 text-indigo-600 hover:underline text-xs">Tạo lại QR</button>
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
    </AuthenticatedLayout>
</template>
