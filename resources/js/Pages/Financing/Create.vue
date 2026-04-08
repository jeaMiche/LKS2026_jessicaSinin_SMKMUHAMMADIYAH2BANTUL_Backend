<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    maxLoan: Number,
    revenue: Number,
})

const form = useForm({
    amount:       '',
    tenor_months: '12',
    purpose:      '',
    omzet:        props.revenue,
})


const rateByTenor = {
    '3':  9,
    '6':  10,
    '12': 11,
    '24': 12,
    '36': 13,
    '48': 14,
    '60': 15,
}

const currentRate = computed(() => rateByTenor[String(form.tenor_months)] ?? 12)

const previewInstallment = computed(() => {
    const amount = parseFloat(form.amount)
    const tenor  = parseInt(form.tenor_months)
    const rate   = currentRate.value

    if (!amount || !tenor || amount <= 0) return 0

    const r = rate / 100 / 12
    if (r === 0) return Math.round(amount / tenor)

    const f = Math.pow(1 + r, tenor)
    return Math.round(amount * (r * f) / (f - 1))
})

const formatRp = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(val ?? 0)

const submit = () => {
    form.post(route('financing.store'))
}
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Ajukan Pinjaman</h1>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-700">
                    Omzet Bulanan Anda: <strong>{{ formatRp(revenue) }}</strong> |
                    Maks. Pinjaman: <strong>{{ formatRp(maxLoan) }}</strong>
                </p>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-xl shadow p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Pinjaman (Rp)
                    </label>
                    <input
                        v-model="form.amount"
                        type="number"
                        :max="maxLoan"
                        min="1000000"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2
                               focus:ring-blue-500 outline-none"
                        placeholder="Contoh: 50000000"
                    />
                    <p v-if="form.errors.amount" class="text-red-500 text-sm mt-1">
                        {{ form.errors.amount }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tenor
                    </label>
                    <select
                        v-model="form.tenor_months"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2
                               focus:ring-blue-500 outline-none">
                        <option value="" disabled>-- Pilih Tenor --</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan</option>
                        <option value="24">24 Bulan</option>
                    </select>
                    <p v-if="form.errors.tenor_months" class="text-red-500 text-sm mt-1">
                        {{ form.errors.tenor_months }}
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Suku Bunga</p>
                    <p class="text-lg font-bold text-gray-800">
                        {{ currentRate }}% / tahun
                        <span class="text-xs font-normal text-gray-400">(ditetapkan sistem)</span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tujuan Pinjaman
                    </label>
                    <textarea
                        v-model="form.purpose"
                        rows="3"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2
                               focus:ring-blue-500 outline-none"
                        placeholder="Jelaskan tujuan penggunaan dana..."
                    />
                    <p v-if="form.errors.purpose" class="text-red-500 text-sm mt-1">
                        {{ form.errors.purpose }}
                    </p>
                </div>

                <div v-if="previewInstallment > 0"
                     class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-700">Estimasi Cicilan per Bulan:</p>
                    <p class="text-2xl font-bold text-green-800">
                        {{ formatRp(previewInstallment) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        Bunga {{ currentRate }}% / tahun · Tenor {{ form.tenor_months }} bulan
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-700 text-white py-3 rounded-lg font-semibold
                           hover:bg-blue-800 transition disabled:opacity-50
                           disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg v-if="form.processing" class="animate-spin h-5 w-5"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span>{{ form.processing ? 'Mengirim...' : 'Ajukan Pinjaman' }}</span>
                </button>

            </form>
        </div>
    </AppLayout>
</template>