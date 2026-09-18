<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Bảng điều khiển" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Bảng điều khiển (Dashboard)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6 text-gray-900 font-medium text-lg">
                        Chào mừng bạn đến với Hệ thống Quản lý Quán Cà phê! ☕
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Quick Action 1 -->
                    <Link :href="route('admin.cashier.index')" class="block p-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-200 hover:shadow-md hover:ring-indigo-500 transition">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">💰 Thu ngân (Cashier)</h3>
                        <p class="text-gray-600">Quản lý bàn, nhận thanh toán và xuất hóa đơn.</p>
                    </Link>

                    <!-- Quick Action 2 -->
                    <Link :href="route('admin.kitchen.index')" class="block p-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-200 hover:shadow-md hover:ring-orange-500 transition">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">👨‍🍳 Bếp (Kitchen)</h3>
                        <p class="text-gray-600">Màn hình nhận order Real-time từ khách hàng.</p>
                    </Link>

                    <!-- Quick Action 3 -->
                    <Link :href="route('admin.reports.index')" class="block p-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-200 hover:shadow-md hover:ring-emerald-500 transition">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">📈 Thống kê</h3>
                        <p class="text-gray-600">Xem báo cáo doanh thu và các món bán chạy.</p>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
