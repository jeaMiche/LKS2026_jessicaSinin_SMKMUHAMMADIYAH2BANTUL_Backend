<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    user: Object,
})

const form = useForm({
    name:            props.user.name,
    business_name:   props.user.business_name ?? '',
    monthly_revenue: props.user.monthly_revenue ?? '',
})

const submit = () => {
    form.patch(route('profile.umkm.update'))
}
</script>

<template>
    <AppLayout>
        <div class="max-w-xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data UMKM</h1>

            <div class="bg-white rounded-xl shadow p-6">
                <form @submit.prevent="submit" class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2
                                   focus:ring-blue-500 outline-none"
                        />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Usaha
                        </label>
                        <input
                            v-model="form.business_name"
                            type="text"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2
                                   focus:ring-blue-500 outline-none"
                        />
                        <p v-if="form.errors.business_name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.business_name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Omzet Bulanan (Rp)
                        </label>
                        <input
                            v-model="form.monthly_revenue"
                            type="number"
                            min="100000"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2
                                   focus:ring-blue-500 outline-none"
                            placeholder="Contoh: 30000000"
                        />
                        <p v-if="form.errors.monthly_revenue" class="text-red-500 text-sm mt-1">
                            {{ form.errors.monthly_revenue }}
                        </p>
                        
                        <p class="text-xs text-gray-400 mt-1">
                            Maks. pinjaman setelah update:
                            <strong class="text-blue-600">
                                Rp {{ (form.monthly_revenue * 3).toLocaleString('id-ID') }}
                            </strong>
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-blue-700 text-white py-3 rounded-lg font-semibold
                               hover:bg-blue-800 transition disabled:opacity-50
                               flex items-center justify-center gap-2"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                    </button>

                </form>
            </div>
        </div>
    </AppLayout>
</template>