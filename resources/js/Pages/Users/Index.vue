<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import ModernTable from '@/Components/ModernTable.vue';

const props = defineProps({
    users: Array,
    isImpersonating: Boolean,
});

// Impersonate user
const impersonate = (userId) => {
    if (confirm('Login sebagai user ini?')) {
        useForm({}).post(route('users.impersonate', userId));
    }
};

// Stop impersonation
const stopImpersonation = () => {
    useForm({}).post(route('users.stopImpersonation'));
};

// Table columns
const columns = [
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role_label', label: 'Role', sortable: false },
    { key: 'is_active', label: 'Status', sortable: false },
    { key: 'created_at', label: 'Terdaftar', sortable: true },
    { key: 'actions', label: 'Actions', sortable: false },
];
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                    👥 User Management
                </h2>
                <button
                    v-if="isImpersonating"
                    @click="stopImpersonation"
                    class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                >
                    ← Kembali ke Admin
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Impersonation Alert -->
                <div v-if="isImpersonating" class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <h3 class="font-semibold text-amber-900">Mode Impersonation Aktif</h3>
                            <p class="text-sm text-amber-800">Anda sedang login sebagai user lain. Klik "Kembali ke Admin" untuk kembali.</p>
                        </div>
                    </div>
                </div>

                <Card padding="sm">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Daftar Pengguna</h3>
                        <p class="text-slate-600 mb-4">Kelola akses pengguna dan impersonate untuk testing.</p>
                    </div>
                    
                    <ModernTable
                        :columns="columns"
                        :data="users"
                        hoverable
                        striped
                    >
                        <template #cell-role_label="{ value, row }">
                            <Badge
                                :variant="row.role === 'super_admin' ? 'primary' : row.role === 'ketua' ? 'info' : row.role === 'bendahara' ? 'success' : 'neutral'"
                                size="sm"
                            >
                                {{ value }}
                            </Badge>
                        </template>

                        <template #cell-is_active="{ value }">
                            <Badge :variant="value ? 'success' : 'danger'" size="sm" dot>
                                {{ value ? 'Active' : 'Inactive' }}
                            </Badge>
                        </template>

                        <template #cell-actions="{ row }">
                            <button
                                @click="impersonate(row.id)"
                                class="text-sm bg-primary-100 text-primary-700 hover:bg-primary-200 px-3 py-1 rounded-md transition-colors"
                            >
                                🔑 Login As
                            </button>
                        </template>
                    </ModernTable>
                </Card>

                <!-- Info Card -->
                <Card padding="lg" class="mt-6">
                    <div class="flex items-start gap-4">
                        <span class="text-4xl">💡</span>
                        <div>
                            <h4 class="font-bold text-slate-800 mb-2">Tentang Impersonation</h4>
                            <ul class="text-sm text-slate-600 space-y-1">
                                <li>• <strong>Login As:</strong> Masuk sebagai user lain untuk testing tanpa password</li>
                                <li>• <strong>Segregation:</strong> Setiap role memiliki akses berbeda (bendahara, ketua, marbot)</li>
                                <li>• <strong>Audit:</strong> Semua aktivitas impersonation tercatat di session</li>
                                <li>• <strong>Security:</strong> Hanya Super Admin yang bisa impersonate</li>
                            </ul>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
