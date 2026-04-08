<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    loan: Object,
})

const formatRp = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val ?? 0)


const form = useForm({
    method: 'transfer',
})

function submit() {
    if (!props.loan) return
    form.post(route('loans.payments.store', props.loan.id), {
        onSuccess: () => {
            window.location.href = route('loans.show', props.loan.id)
        }
    })
}
</script>

<template>
    <AppLayout>
        <div class="max-w-md mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Pembayaran Cicilan</h1>

            <div class="bg-white rounded-xl shadow p-6 mb-6 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Jumlah Pinjaman</span>
                    <span class="font-semibold">{{ formatRp(loan.amount) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sisa Hutang</span>
                    <span class="font-semibold text-red-600">{{ formatRp(loan.outstanding_balance) }}</span>
                </div>

                <!-- Jumlah cicilan ditampilkan, tidak bisa diubah -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <p class="text-sm text-blue-500 mb-1">Jumlah yang harus dibayar:</p>
                    <p class="text-2xl font-bold text-blue-800">
                        {{ formatRp(loan.monthly_installment) }}
                    </p>
                    <p class="text-xs text-blue-400 mt-1">(ditetapkan sistem)</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Metode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Metode Pembayaran
                        </label>
                        <select
                            v-model="form.method"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2
                                   focus:ring-purple-500 outline-none"
                        >
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold
                               hover:bg-purple-700 transition disabled:opacity-50
                               disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>{{ form.processing ? 'Memproses...' : 'Bayar Sekarang' }}</span>
                    </button>

                </form>
            </div>
        </div>
    </AppLayout>
</template>