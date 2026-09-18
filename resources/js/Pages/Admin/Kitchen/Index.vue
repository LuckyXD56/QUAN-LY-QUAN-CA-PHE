<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    initialItems: Array,
});

const orderItems = ref(props.initialItems);

const playSound = () => {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'bell'; // fallback to sine
        osc.frequency.setValueAtTime(880, ctx.currentTime); // A5 note
        osc.frequency.setValueAtTime(1108.73, ctx.currentTime + 0.1); // C#6 note
        gain.gain.setValueAtTime(0.5, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start();
        osc.stop(ctx.currentTime + 0.5);
    } catch (e) {
        console.error("Audio play failed", e);
    }
};

// Setup Real-time listening
onMounted(() => {
    if (window.Echo) {
        window.Echo.channel('kitchen')
            .listen('.order.placed', (e) => {
                playSound();
                // Thêm các món mới vào danh sách hiện tại
                const newOrder = e.order;
                newOrder.items.forEach(item => {
                    // Add table info to item to display in the UI
                    item.order = {
                        session: newOrder.session,
                        table: newOrder.session.table
                    };
                    orderItems.value.push(item);
                });
                
                // Play notification sound
                try {
                    const audio = new Audio('/kitchen-bell.mp3'); // Need to put an MP3 in public folder in real usage
                    audio.play().catch(e => console.log('Autoplay blocked'));
                } catch (err) {}
            });
    }
});

onUnmounted(() => {
    if (window.Echo) {
        window.Echo.leave('kitchen');
    }
});

const updateStatus = (item, status) => {
    useForm({ status: status }).patch(route('admin.kitchen.update-status', item.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Update local state without reloading everything
            item.status = status;
            // If finished, remove from screen after a delay
            if (status === 'ready') {
                setTimeout(() => {
                    orderItems.value = orderItems.value.filter(i => i.id !== item.id);
                }, 1500);
            }
        }
    });
};
</script>

<template>
    <Head title="Màn hình Bếp (Real-time)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
                    Màn hình Bếp
                    <span class="ml-3 flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <span class="ml-2 text-sm text-red-500">Live</span>
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Ticket Item -->
                    <div 
                        v-for="item in orderItems" 
                        :key="item.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden"
                        :class="item.status === 'pending' ? 'ring-1 ring-red-500' : 'ring-1 ring-yellow-400'"
                    >
                        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                            <div class="font-bold text-lg text-gray-800">
                                {{ item.order?.session?.table?.name || 'Bàn ?' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ new Date(item.created_at).toLocaleTimeString() }}
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ item.product?.name }}</h3>
                                    <div v-if="item.notes" class="mt-2 text-red-600 bg-red-50 p-2 rounded text-sm italic">
                                        Lưu ý: {{ item.notes }}
                                    </div>
                                </div>
                                <div class="bg-gray-800 text-white font-bold rounded-full h-10 w-10 flex items-center justify-center text-lg shadow-inner">
                                    x{{ item.quantity }}
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                            <div>
                                <span v-if="item.status === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Mới
                                </span>
                                <span v-if="item.status === 'cooking'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Đang làm
                                </span>
                                <span v-if="item.status === 'ready'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Xong
                                </span>
                            </div>
                            <div class="space-x-2">
                                <button v-if="item.status === 'pending'" @click="updateStatus(item, 'cooking')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow font-semibold text-sm transition">
                                    Nấu
                                </button>
                                <button v-if="item.status === 'cooking'" @click="updateStatus(item, 'ready')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow font-semibold text-sm transition">
                                    Xong
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="orderItems.length === 0" class="col-span-full py-12 text-center text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có order nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Bếp đang rảnh rỗi. Đang chờ món mới...</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
