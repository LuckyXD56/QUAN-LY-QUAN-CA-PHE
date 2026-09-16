<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    name: '',
    description: '',
    sort_order: 0,
});

const submit = () => {
    form.post(route('admin.categories.store'), {
        onSuccess: () => form.reset(),
    });
};

const deleteCategory = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa danh mục này?')) {
        useForm({}).delete(route('admin.categories.destroy', id));
    }
};
</script>

<template>
    <Head title="Quản lý Danh mục" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Quản lý Danh mục (Categories)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Add Category Form -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thêm Danh Mục Mới</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Tên danh mục" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                        </div>
                        <div>
                            <InputLabel for="description" value="Mô tả" />
                            <TextInput
                                id="description"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.description"
                            />
                        </div>
                        <div>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Thêm
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Category List -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Danh Sách Danh Mục</h3>
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th class="px-6 py-3">ID</th>
                                <th class="px-6 py-3">Tên</th>
                                <th class="px-6 py-3">Mô tả</th>
                                <th class="px-6 py-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in categories" :key="category.id" class="border-b bg-white">
                                <td class="px-6 py-4">{{ category.id }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ category.name }}</td>
                                <td class="px-6 py-4">{{ category.description }}</td>
                                <td class="px-6 py-4">
                                    <button @click="deleteCategory(category.id)" class="text-red-600 hover:underline">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
