<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    financings: Object,
    userRole: String,
    canReview: Boolean,
    canApprove: Boolean,
    canApplyfinancing: Boolean,
    business: Boolean, 
})


const processing = ref(null)

const formatRp = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(val ?? 0)

const statusColor = {
    draft: 'bg-gray-100 text-gray-700',
    submitted: 'bg-blue-100 text-blue-700',
    verified: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
    lunas: 'bg-purple-100 text-purple-700',
}

const statusLabel = {
    draft: 'Draft',
    submitted: 'Terkirim',
    verified: 'Terverifikasi',
    rejected: 'Ditolak',
    lunas: 'Lunas',
}

const action = (url, financingId) => {
    processing.value = financingId
    router.patch(url, {}, {
        onFinish: () => { processing.value = null }
    })
}

const confirmDelete = (financing) => {
    if (!confirm(`Hapus pinjaman ${financing.user?.name}? Data bisa dipulihkan.`)) return
    processing.value = financing.id
    router.delete(route('financing.destroy', financing.id), {
        onFinish: () => { processing.value = null }
    })
}

const isProcessing = (financingId) => processing.value === financingId
</script>

<template>
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Daftar Pinjaman</h1>
            <Link v-if="userRole === 'applicant' && business" :href="route('financing.create')" class="bg-blue-700 text-white px-4 py-2 rounded">
              Ajukan Pinjaman
            </Link>
        </div>


        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Pemohon</th>
                        <th class="px-6 py-3 text-left">Jumlah</th>
                        <th class="px-6 py-3 text-left">Cicilan/Bulan</th>
                        <th class="px-6 py-3 text-left">Tenor</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="financing in financings.data" :key="financing.id">
                        <td class="px-6 py-4 font-medium">{{ financing.user?.name }}</td>
                        <td class="px-6 py-4">{{ formatRp(financing.amount) }}</td>
                        <td class="px-6 py-4 font-semibold text-green-700">
                            {{ formatRp(financing.monthly_installment) }}
                        </td>
                        <td class="px-6 py-4">{{ financing.tenor_months }} bln</td>
                        <td class="px-6 py-4">
                            <span :class="['capitalize text-xs font-semibold px-2 py-1 rounded-full',
                                statusColor[financing.status] ?? 'bg-gray-100 text-gray-600']">
                                {{ statusLabel[financing.status] ?? financing.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                             <Link :href="route('financing.show', financing.id)"
                                class="bg-gray-500 text-white text-xs px-3 py-1 rounded hover:bg-gray-600 transition">
                                Detail
                            </Link>

                            <button v-if="(canApprove) || (userRole === 'applicant' && ['draft', 'submitted'].includes(financing.status))" 
                                :disabled="isProcessing(financing.id)"
                                @click="confirmDelete(financing)" 
                                class="bg-red-700 text-white text-xs px-3 py-1 rounded hover:bg-red-800 transition disabled:opacity-50">
                                {{ isProcessing(financing.id) ? '...' : 'Hapus' }}
                            </button>

                            <button v-if="canReview && financing.status === 'submitted'" :disabled="isProcessing(financing.id)"
                                @click="action(route('financing.approve', financing.id), financing.id)" 
                                class="bg-green-500 text-white text-xs px-3 py-1 rounded hover:bg-green-600 transition disabled:opacity-50">
                                {{ isProcessing(financing.id) ? '...' : 'Verifikasi' }}
                            </button>

                            <button v-if="canApprove && ['submitted'].includes(financing.status)"
                                :disabled="isProcessing(financing.id)"
                                @click="action(route('financing.reject', financing.id), financing.id)" 
                                class="bg-red-500 text-white text-xs px-3 py-1 rounded hover:bg-red-600 transition disabled:opacity-50">
                                {{ isProcessing(financing.id) ? '...' : 'Tolak' }}
                            </button>

                            <Link v-if="userRole === 'applicant' && financing.status === 'verified'"
                                :href="route('financing.payments.create', financing.id)"
                                class="bg-purple-600 text-white text-xs px-3 py-1 rounded hover:bg-purple-700 transition">
                                Bayar Cicilan
                            </Link>

                            <span v-if="financing.status === 'lunas'" class="text-green-600 text-xs font-semibold">
                                ✅ Lunas
                            </span>
                        </td>
                    </tr>

                     <tr v-if="financings.data.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            Belum ada data pinjaman.
                        </td>
                    </tr>
                </tbody>
            </table>

             <div class="px-6 py-4 border-t flex gap-2">
                <a v-for="link in financings.links" :key="link.label" :href="link.url" v-html="link.label" 
                    :class="['px-3 py-1 rounded text-sm border transition',
                    link.active ? 'bg-blue-700 text-white border-blue-700' : 'text-gray-600 hover:bg-gray-100',
                    !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : '']" />
            </div>
        </div>
    </AppLayout>
</template>