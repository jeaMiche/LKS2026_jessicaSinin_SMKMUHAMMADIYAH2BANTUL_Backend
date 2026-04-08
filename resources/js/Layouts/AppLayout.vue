<script setup>
import { usePage, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const user = computed(() => usePage().props.auth.user)
const canApplyfinancing = computed(() => usePage().props.canApplyfinancing)

const menus = computed(() => {
    const base = [
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'Pinjaman', href: route('financing.index') },
    ]

    if (user.value.role === 'applicant') {
        if (canApplyfinancing.value) {
            base.push({ label: 'Ajukan Pinjaman', href: route('financing.create') })
        }
        base.push({ label: 'Data UMKM', href: route('profile.edit') })
    }

    if (user.value.role === 'admin') {
        base.push({ label: 'Data Terhapus', href: route('financing.trashed') })
    }

    return base
})

const logout = () => router.post(route('logout'))
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <aside class="w-64 bg-blue-900 text-white flex flex-col">
            <div class="p-6 text-xl font-bold border-b border-blue-700">
                Digital UMKM
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <Link
                    v-for="menu in menus"
                    :key="menu.href"
                    :href="menu.href"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                >
                    <span v-if="menu.icon">{{ menu.icon }}</span>
                    <span>{{ menu.label }}</span>
                </Link>
            </nav>

            <div class="p-4 border-t border-blue-700">
                <p class="font-semibold text-sm">{{ user.name }}</p>
                <p class="text-xs text-blue-300 mb-3">{{ user.email }}</p>
                <span class="capitalize bg-blue-600 text-xs px-2 py-0.5 rounded-full">
                    {{ user.role }}
                </span>

                <button
                    @click="logout"
                    class="w-full mt-4 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 transition text-sm font-medium"
                >
                    Logout
                </button>
            </div>
        </aside>

        <main class="flex-1 p-8">
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm"
            >
                ✅ {{ $page.props.flash.success }}
            </div>

            <div
                v-if="$page.props.flash?.error"
                class="mb-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm"
            >
                ❌ {{ $page.props.flash.error }}
            </div>

            <slot />
        </main>
    </div>
</template>