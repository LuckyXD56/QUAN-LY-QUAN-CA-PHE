<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    products: Array,
    categories: Array,
});

const form = useForm({
    category_id: '',
    name: '',
    price: '',
    description: '',
    image: '',
    is_available: true,
});

const submit = () => {
    form.post(route('admin.products.store'), {
        onSuccess: () => form.reset(),
    });
};

const deleteProduct = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa món này?')) {
        useForm({}).delete(route('admin.products.destroy', id));
    }
};
</script>

<template>
    <Head title="Quản lý Món ăn" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Quản lý Món ăn (Products)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Add Product Form -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thêm Món Mới</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="category" value="Danh mục" />
                            <select v-model="form.category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="" disabled>Chọn danh mục</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="name" value="Tên món" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                        </div>
                        <div>
                            <InputLabel for="price" value="Giá (VNĐ)" />
                            <TextInput id="price" type="number" class="mt-1 block w-full" v-model="form.price" required />
                        </div>
                        <div>
                            <InputLabel for="image" value="URL Hình ảnh (Image URL)" />
                            <TextInput id="image" type="text" class="mt-1 block w-full" v-model="form.image" placeholder="https://..." />
                        </div>
                        <div>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Thêm
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Product List -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Danh Sách Món Ăn</h3>
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th class="px-6 py-3">ID</th>
                                <th class="px-6 py-3">Danh mục</th>
                                <th class="px-6 py-3">Tên món</th>
                                <th class="px-6 py-3">Giá</th>
                                <th class="px-6 py-3">Trạng thái</th>
                                <th class="px-6 py-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id" class="border-b bg-white">
                                <td class="px-6 py-4">{{ product.id }}</td>
                                <td class="px-6 py-4">{{ product.category?.name }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ product.name }}</td>
                                <td class="px-6 py-4">{{ product.price }}</td>
                                <td class="px-6 py-4">{{ product.is_available ? 'Đang bán' : 'Hết hàng' }}</td>
                                <td class="px-6 py-4">
                                    <button @click="deleteProduct(product.id)" class="text-red-600 hover:underline">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
