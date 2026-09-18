<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    revenueData: Array,
    topProducts: Array,
    todaySummary: Object
});
</script>

<template>
    <Head title="Thống kê & Báo cáo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Thống kê & Báo cáo Doanh thu
                </h2>
                <a :href="route('admin.reports.export')" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow">
                    📥 Xuất Excel (CSV)
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-indigo-600 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-medium opacity-80">Doanh thu hôm nay</h3>
                        <p class="text-4xl font-bold mt-2">{{ todaySummary.revenue.toLocaleString() }} ₫</p>
                    </div>
                    <div class="bg-emerald-600 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-medium opacity-80">Số đơn (hóa đơn) hôm nay</h3>
                        <p class="text-4xl font-bold mt-2">{{ todaySummary.orders }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Revenue Table -->
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Doanh thu 7 ngày gần nhất</h3>
                        <table class="w-full text-left text-sm text-gray-500">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2">Ngày</th>
                                    <th class="px-4 py-2 text-right">Doanh thu (₫)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in revenueData" :key="row.date" class="border-b">
                                    <td class="px-4 py-3">{{ row.date }}</td>
                                    <td class="px-4 py-3 text-right font-bold">{{ parseFloat(row.total).toLocaleString() }}</td>
                                </tr>
                                <tr v-if="revenueData.length === 0">
                                    <td colspan="2" class="px-4 py-3 text-center text-gray-400">Chưa có dữ liệu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Top Products Table -->
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Top 5 Món bán chạy nhất</h3>
                        <table class="w-full text-left text-sm text-gray-500">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2">Tên món</th>
                                    <th class="px-4 py-2 text-center">Đã bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in topProducts" :key="item.name" class="border-b">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ item.name }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-indigo-100 text-indigo-800 py-1 px-2 rounded-full font-bold">{{ item.sold }}</span>
                                    </td>
                                </tr>
                                <tr v-if="topProducts.length === 0">
                                    <td colspan="2" class="px-4 py-3 text-center text-gray-400">Chưa có dữ liệu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
