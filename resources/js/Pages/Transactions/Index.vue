<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import { useToast } from '@/Composables/useToast';

const toast = useToast();

// Form state
const form = useForm({
    type: 'income',
    category: '',
    amount: '',
    description: '',
    date: new Date().toISOString().split('T')[0],
    proof_image: null,
});

const imagePreview = ref(null);
const fileInput = ref(null);

// Watch type changes to reset proof_image for income
watch(() => form.type, (newType) => {
    if (newType === 'income') {
        form.proof_image = null;
        imagePreview.value = null;
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
});

// Handle file selection
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            toast.error('Ukuran file maksimal 5MB');
            event.target.value = '';
            return;
        }
        form.proof_image = file;
        const reader = new FileReader();
        reader.onload = (e) => { imagePreview.value = e.target.result; };
        reader.readAsDataURL(file);
    }
};

// Submit form
const submit = () => {
    form.post(route('transactions.store'), {
        forceFormData: true,
        onSuccess: () => {
            toast.success('Transaksi berhasil disimpan!');
            form.reset();
            imagePreview.value = null;
            if (fileInput.value) fileInput.value.value = '';
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            toast.error(firstError || 'Gagal menyimpan transaksi');
        },
    });
};

const categories = {
    income: ['Kotak Jumat', 'Infaq', 'Sedekah', 'Wakaf', 'Donasi', 'Lainnya'],
    expense: ['Operasional', 'Listrik & Air', 'Renovasi', 'Gaji Marbot', 'Kebersihan', 'Lainnya'],
};
</script>

<template>
    <Head title="Input Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                💵 Input Transaksi Keuangan
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <Card padding="lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-3">Tipe Transaksi <span class="text-red-500">*</span></label>
                            <div class="flex gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input v-model="form.type" type="radio" value="income" class="w-4 h-4 text-emerald-600" />
                                    <span class="ml-2 text-slate-700">💰 Pemasukan</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input v-model="form.type" type="radio" value="expense" class="w-4 h-4 text-red-600" />
                                    <span class="ml-2 text-slate-700">💸 Pengeluaran</span>
                                </label>
                            </div>
                            <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                            <select v-model="form.category" class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Pilih Kategori</option>
                                <option v-for="cat in categories[form.type]" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                            <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500">Rp</span>
                                <input v-model="form.amount" type="number" min="0" step="1" class="w-full pl-12 rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" />
                            </div>
                            <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan</label>
                            <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Detail transaksi..."></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                            <input v-model="form.date" type="date" class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500" />
                            <p v-if="form.errors.date" class="mt-1 text-sm text-red-600">{{ form.errors.date }}</p>
                        </div>

                        <!-- Proof Image (for expense only) -->
                        <div v-if="form.type === 'expense'" class="border-2 border-dashed border-amber-300 rounded-lg p-6 bg-amber-50">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                📸 Bukti Transaksi <span class="text-red-500">*</span>
                                <span class="text-xs text-slate-500 font-normal">(Wajib untuk pengeluaran)</span>
                            </label>
                            <input ref="fileInput" type="file" @change="handleFileChange" accept="image/*,.pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-700 hover:file:bg-primary-200" />
                            <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG, PDF. Maksimal 5MB</p>
                            <p v-if="form.errors.proof_image" class="mt-1 text-sm text-red-600">{{ form.errors.proof_image }}</p>
                            
                            <!-- Image Preview -->
                            <div v-if="imagePreview" class="mt-4">
                                <p class="text-sm text-slate-600 mb-2">Preview:</p>
                                <img :src="imagePreview" alt="Preview" class="max-h-48 rounded-lg shadow-md" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 pt-4">
                            <button type="submit" :disabled="form.processing" class="btn-primary flex-1" :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>💾 Simpan Transaksi</span>
                            </button>
                            <button type="button" @click="$inertia.visit(route('keuangan.index'))" class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </Card>
            </div>
        </div>

        <!-- Toast Notification -->
        <Transition name="fade">
            <div v-if="toast.show.value" :class="['fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg max-w-sm', toast.type.value === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white']">
                <div class="flex items-center gap-3">
                    <span v-if="toast.type.value === 'success'" class="text-2xl">✅</span>
                    <span v-else class="text-2xl">❌</span>
                    <p class="font-medium">{{ toast.message.value }}</p>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
