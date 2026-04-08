<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({ canLogin: Boolean, canRegister: Boolean });

const omzet = ref(5000000);
const limit = computed(() => omzet.value * 3);
const rp = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);
</script>

<template>

    <Head title="Selamat Datang" />
    <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6 text-gray-800">
        <div class="text-center max-w-2xl w-full">
            <div
                class="mb-6 inline-flex w-14 h-14 bg-blue-600 rounded-2xl items-center justify-center text-white shadow-lg font-bold text-2xl">
                M</div>
            <h1 class="text-3xl font-bold mb-2">ModalKu Digital</h1>
            <p class="text-gray-500 mb-10">Geser untuk cek potensi modal usaha Anda.</p>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <div class="mb-10 text-left">
                    <div class="flex justify-between items-end mb-4">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Omzet Bulanan
                            Anda</label>
                        <span class="text-xl font-bold text-blue-600">{{ rp(omzet) }}</span>
                    </div>
                    <input v-model="omzet" type="range" min="1000000" max="50000000" step="500000"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600" />
                    <div class="flex justify-between text-[10px] text-gray-400 mt-2 font-bold">
                        <span>1 JUTA</span><span>50 JUTA</span>
                    </div>
                </div>

                <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 mb-8 text-center">
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-widest">Estimasi Limit Pinjaman</p>
                    <p class="text-4xl font-extrabold text-blue-900 mt-2">{{ rp(limit) }}</p>
                </div>

                <div class="flex flex-col gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')"
                            class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 transition">
                            Ajukan Sekarang</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('register')"
                            class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 transition">
                            Daftar & Ajukan Pinjaman</Link>
                        <Link :href="route('login')"
                            class="text-sm font-semibold text-gray-400 hover:text-blue-600 transition">Sudah punya akun?
                            Masuk</Link>
                    </template>
                </div>
            </div>

        </div>
    </div>
</template>

<style>
input[type='range']::-webkit-slider-thumb {
    width: 24px;
    height: 24px;
    background: #2563eb;
    border-radius: 50%;
    cursor: pointer;
    border: 4px solid white;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}
</style>