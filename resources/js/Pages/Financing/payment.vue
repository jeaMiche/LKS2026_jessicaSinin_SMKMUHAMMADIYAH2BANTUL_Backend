<!-- resources/js/Pages/Payment/Index.vue -->
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    loan: Object,      
    schedule: Array,   
    payments: Array,  
})

const activeTab = ref('schedule') // 'schedule' | 'history'

const formatRp = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(val ?? 0)

const formatDate = (d) =>
    d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'

const paidPercent = computed(() => {
    if (!props.loan) return 0
    const total = props.loan.amount
    const outstanding = props.loan.outstanding_balance
    return Math.min(100, Math.round(((total - outstanding) / total) * 100))
})

const nextInstallment = computed(() =>
    props.schedule?.find(s => s.status === 'pending') ?? null
)

const form = useForm({
    amount: '',
    method: 'transfer',
    note: '',
})

function submitPayment() {
    form.post(route('loans.pay', props.loan.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}

const methodLabel = { transfer: 'Transfer Bank', cash: 'Tunai', qris: 'QRIS' }

const statusStyle = {
    paid:    'bg-emerald-900/40 text-emerald-400 border border-emerald-700/40',
    pending: 'bg-yellow-900/40  text-yellow-400  border border-yellow-700/40',
    overdue: 'bg-red-900/40     text-red-400     border border-red-700/40',
}
const statusLabel = { paid: 'Lunas', pending: 'Belum Bayar', overdue: 'Jatuh Tempo' }
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-8">

            <!-- ── Page header ── -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pembayaran Cicilan</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola cicilan pinjaman aktif Anda</p>
            </div>

            <!-- ── No active loan ── -->
            <div v-if="!loan"
                 class="bg-white rounded-2xl shadow border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24">
                        <path d="M9 12h6m-3-3v6M12 3a9 9 0 100 18A9 9 0 0012 3z"
                              stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada pinjaman aktif saat ini.</p>
                <a :href="route('loans.create')"
                   class="inline-block mt-4 bg-blue-600 text-white text-sm font-semibold
                          px-6 py-2.5 rounded-lg hover:bg-blue-700 transition">
                    Ajukan Pinjaman
                </a>
            </div>

            <template v-if="loan">

                <!-- ── Loan summary card ── -->
                <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-2xl p-6 text-white shadow-lg">
                    <div class="flex flex-wrap gap-6 justify-between mb-6">
                        <div>
                            <p class="text-blue-200 text-xs uppercase tracking-wider font-medium mb-1">Total Pinjaman</p>
                            <p class="text-3xl font-bold">{{ formatRp(loan.amount) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-200 text-xs uppercase tracking-wider font-medium mb-1">Sisa Kewajiban</p>
                            <p class="text-3xl font-bold text-yellow-300">{{ formatRp(loan.outstanding_balance) }}</p>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div>
                        <div class="flex justify-between text-xs text-blue-200 mb-1.5">
                            <span>Progres Pelunasan</span>
                            <span>{{ paidPercent }}%</span>
                        </div>
                        <div class="h-2.5 bg-blue-950/50 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-300 rounded-full transition-all duration-700"
                                 :style="{ width: paidPercent + '%' }" />
                        </div>
                    </div>

                    <!-- Meta info -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-blue-600">
                        <div>
                            <p class="text-blue-300 text-xs mb-0.5">Tenor</p>
                            <p class="font-semibold text-sm">{{ loan.tenor }} bulan</p>
                        </div>
                        <div>
                            <p class="text-blue-300 text-xs mb-0.5">Bunga</p>
                            <p class="font-semibold text-sm">{{ loan.interest_rate }}% / bln</p>
                        </div>
                        <div>
                            <p class="text-blue-300 text-xs mb-0.5">Tgl Mulai</p>
                            <p class="font-semibold text-sm">{{ formatDate(loan.start_date) }}</p>
                        </div>
                        <div>
                            <p class="text-blue-300 text-xs mb-0.5">Status</p>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20
                                         text-emerald-300 border border-emerald-500/30 capitalize">
                                {{ loan.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ── Next installment + form ── -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Next installment info -->
                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                        <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                            Cicilan Berikutnya
                        </h2>
                        <template v-if="nextInstallment">
                            <p class="text-3xl font-bold text-gray-800 mb-1">
                                {{ formatRp(nextInstallment.amount) }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Jatuh tempo:
                                <span class="font-semibold text-red-500">
                                    {{ formatDate(nextInstallment.due_date) }}
                                </span>
                            </p>
                        </template>
                        <p v-else class="text-sm text-emerald-600 font-medium">
                            🎉 Semua cicilan sudah lunas!
                        </p>
                    </div>

                    <!-- Payment form -->
                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                        <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                            Bayar Cicilan
                        </h2>
                        <form @submit.prevent="submitPayment" class="space-y-3">

                            <!-- Nominal -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Nominal (Rp)</label>
                                <input v-model="form.amount"
                                       type="number"
                                       placeholder="Masukkan nominal..."
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                              transition"
                                       required />
                                <p v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</p>
                            </div>

                            <!-- Metode -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Metode Pembayaran</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button v-for="(label, val) in methodLabel" :key="val"
                                            type="button"
                                            @click="form.method = val"
                                            class="py-2 rounded-xl text-xs font-semibold border transition"
                                            :class="form.method === val
                                                ? 'bg-blue-600 text-white border-blue-600'
                                                : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-blue-300'">
                                        {{ label }}
                                    </button>
                                </div>
                                <p v-if="form.errors.method" class="text-red-500 text-xs mt-1">{{ form.errors.method }}</p>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Catatan (opsional)</label>
                                <input v-model="form.note"
                                       type="text"
                                       placeholder="Contoh: Transfer BCA atas nama..."
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                              transition" />
                            </div>

                            <button type="submit"
                                    :disabled="form.processing"
                                    class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-xl
                                           hover:bg-blue-700 disabled:opacity-50 transition text-sm mt-1">
                                {{ form.processing ? 'Memproses...' : 'Bayar Sekarang' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- ── Tabs: Schedule & History ── -->
                <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">

                    <!-- Tab header -->
                    <div class="flex border-b border-gray-100">
                        <button @click="activeTab = 'schedule'"
                                class="flex-1 py-4 text-sm font-semibold transition border-b-2"
                                :class="activeTab === 'schedule'
                                    ? 'border-blue-600 text-blue-600'
                                    : 'border-transparent text-gray-400 hover:text-gray-600'">
                            Jadwal Cicilan
                        </button>
                        <button @click="activeTab = 'history'"
                                class="flex-1 py-4 text-sm font-semibold transition border-b-2"
                                :class="activeTab === 'history'
                                    ? 'border-blue-600 text-blue-600'
                                    : 'border-transparent text-gray-400 hover:text-gray-600'">
                            Riwayat Pembayaran
                        </button>
                    </div>

                    <!-- Tab: Jadwal Cicilan -->
                    <div v-if="activeTab === 'schedule'" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-3 text-left font-medium">Ke-</th>
                                    <th class="px-6 py-3 text-left font-medium">Jatuh Tempo</th>
                                    <th class="px-6 py-3 text-right font-medium">Nominal</th>
                                    <th class="px-6 py-3 text-center font-medium">Status</th>
                                    <th class="px-6 py-3 text-left font-medium">Dibayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="(item, idx) in schedule" :key="idx"
                                    class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-700">{{ idx + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ formatDate(item.due_date) }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                        {{ formatRp(item.amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                              :class="statusStyle[item.status] ?? statusStyle.pending">
                                            {{ statusLabel[item.status] ?? item.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs">
                                        {{ item.paid_at ? formatDate(item.paid_at) : '-' }}
                                    </td>
                                </tr>
                                <tr v-if="!schedule?.length">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                                        Jadwal cicilan belum tersedia.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tab: Riwayat Pembayaran -->
                    <div v-if="activeTab === 'history'" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-3 text-left font-medium">Tanggal</th>
                                    <th class="px-6 py-3 text-right font-medium">Nominal</th>
                                    <th class="px-6 py-3 text-center font-medium">Metode</th>
                                    <th class="px-6 py-3 text-left font-medium">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="p in payments" :key="p.id"
                                    class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-6 py-4 text-gray-600">{{ formatDate(p.payment_date) }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-emerald-600">
                                        {{ formatRp(p.amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                                     bg-blue-50 text-blue-600 border border-blue-100">
                                            {{ methodLabel[p.method] ?? p.method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs">{{ p.note ?? '-' }}</td>
                                </tr>
                                <tr v-if="!payments?.length">
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">
                                        Belum ada riwayat pembayaran.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </template>
        </div>
    </AppLayout>
</template>