<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    userRole: String,
    stats: Object,
    financing: Object,
})

const user = computed(() => usePage().props.auth.user)

const formatRp = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(val)

const quickForm = useForm({
    amount: '',
    method: 'transfer'
})

function payQuick() {
    if (props.financing) {
        quickForm.patch(route('financing.pay', props.financing.id))
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                Selamat Datang, {{ user.name }}
            </h1>
            <p class="text-gray-500 text-sm mt-1 capitalize">
                Role: <span class="font-semibold text-blue-700">{{ userRole }}</span>
            </p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500">Total Pengajuan</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Menunggu Review</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ stats.pending }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Disetujui</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.approved }}</p>
            </div>
        </div>

        <!-- Applicant Section -->
        <div v-if="userRole === 'applicant'" class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h2 class="font-semibold text-blue-800 mb-2">Informasi Akun Anda</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Nama Usaha</p>
                    <p class="font-semibold">{{ user.business_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Omzet Bulanan</p>
                    <p class="font-semibold text-green-700">
                        {{ formatRp(user.monthly_revenue ?? 0) }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Maks. Pinjaman (3x Omzet)</p>
                    <p class="font-semibold text-blue-700">
                        {{ formatRp((user.monthly_revenue ?? 0) * 3) }}
                    </p>
                </div>
            </div>

            <div v-if="financing" class="mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-blue-800">Detail Pinjaman Aktif</h3>
                    <span :class="{
                        'bg-yellow-100 text-yellow-700': ['submitted', 'under_review', 'recommended', 'rejected_by_analyst'].includes(financing.status),
                        'bg-green-100 text-green-700': financing.status === 'approved',
                        'bg-red-100 text-red-700': financing.status === 'rejected_by_manager'
                    }" class="px-3 py-1 rounded-full text-xs font-bold uppercase">
                        {{ financing.status }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-xl border">
                        <p class="text-xs text-gray-400 font-bold uppercase">Total Pinjaman</p>
                        <p class="text-lg font-bold">{{ formatRp(financing.amount) }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border">
                        <p class="text-xs text-gray-400 font-bold uppercase">Sisa Tagihan</p>
                        <p class="text-lg font-bold text-red-600">{{ formatRp(financing.outstanding_balance) }}</p>
                    </div>
                </div>

                <div v-if="financing.status === 'approved'"
                    class="bg-white p-6 rounded-2xl border-2 border-blue-100 shadow-sm">
                    <h4 class="font-bold text-gray-800 mb-4">Bayar Cicilan Sekarang</h4>

                    <form @submit.prevent="payQuick" class="space-y-4">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <input
                                    v-model="quickForm.amount"
                                    type="number"
                                    placeholder="Masukkan nominal bayar"
                                    class="w-full border-gray-200 rounded-xl focus:ring-blue-500 shadow-sm"
                                />
                            </div>
                            <select v-model="quickForm.method" class="border-gray-200 rounded-xl shadow-sm">
                                <option value="transfer">Transfer</option>
                                <option value="cash">Tunai</option>
                            </select>
                        </div>

                        <button
                            type="submit"
                            :disabled="quickForm.processing || !quickForm.amount"
                            class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 disabled:bg-gray-300 transition"
                        >
                            {{ quickForm.processing ? 'Memproses...' : 'Konfirmasi Pembayaran' }}
                        </button>
                    </form>
                </div>

                <div v-else-if="financing.status === 'pending'"
                    class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-center">
                    <p class="text-sm text-yellow-700 font-medium">
                        ⏳ Pinjaman Anda sedang direview oleh analis. Fitur pembayaran akan muncul setelah disetujui.
                    </p>
                </div>

                <div v-if="financing.payments?.length" class="mt-6">
                    <h4 class="font-semibold text-blue-800 mb-2">Riwayat Pembayaran</h4>
                    <table class="w-full text-sm border">
                        <thead>
                            <tr>
                                <th class="border px-2">Tanggal</th>
                                <th class="border px-2">Nominal</th>
                                <th class="border px-2">Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in financing.payments" :key="p.id">
                                <td class="border px-2">{{ p.payment_date }}</td>
                                <td class="border px-2">{{ formatRp(p.amount) }}</td>
                                <td class="border px-2">{{ p.method }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Analyst Section -->
        <div v-if="userRole === 'analyst'" class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <h2 class="font-semibold text-yellow-800 mb-2">Tugas Anda</h2>
            <p class="text-sm text-gray-600">
                Terdapat <strong>{{ stats.pending }}</strong> pengajuan menunggu review Anda.
            </p>
            <a :href="route('financing.index')"
                class="inline-block mt-4 bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition text-sm font-medium">
                Lihat Daftar Pengajuan
            </a>
        </div>

        <div v-if="userRole === 'admin'" class="bg-green-50 border border-green-200 rounded-xl p-6">
            <h2 class="font-semibold text-green-800 mb-2">Panel Admin</h2>
            <p class="text-sm text-gray-600">
                Kelola seluruh pengajuan pinjaman dan proses persetujuan akhir.
            </p>
            <a :href="route('financing.index')"
                class="inline-block mt-4 bg-green-700 text-white px-6 py-2 rounded-lg hover:bg-green-800 transition text-sm font-medium">
                Kelola Semua Pinjaman
            </a>
        </div>
    </AppLayout>
</template>