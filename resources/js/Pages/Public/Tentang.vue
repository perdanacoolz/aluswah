<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const histories = [
    { year: '1990', title: 'Pendirian', description: 'Masjid Al-Hidayah didirikan di atas tanah wakaf seluas 500m2.' },
    { year: '2005', title: 'Renovasi Besar', description: 'Renovasi total bangunan menjadi 2 lantai untuk menampung jamaah yang semakin banyak.' },
    { year: '2024', title: 'Digitalisasi', description: 'Penerapan sistem manajemen modern MasjidVision.' },
];
</script>

<template>
    <Head title="Tentang Masjid Al-Hidayah" />

    <PublicLayout>

        <!-- Header -->
        <div class="bg-emerald-700 pt-32 pb-16 text-center text-white">
            <h1 class="text-4xl font-bold mb-4">Tentang Kami</h1>
            <p class="text-emerald-100 max-w-2xl mx-auto px-4">
                Sejarah dan visi misi Masjid Al-Hidayah dalam melayani umat.
            </p>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-12">
            <!-- Visi Misi -->
            <div class="grid md:grid-cols-2 gap-8 mb-16">
                <div class="bg-white p-8 rounded-2xl shadow-lg border-t-8 border-emerald-500">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Visi</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Menjadi pusat peradaban umat yang mandiri, transparan, dan rahmatan lil 'alamin.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg border-t-8 border-amber-500">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Misi</h2>
                    <ul class="list-disc list-inside text-slate-600 leading-relaxed space-y-2">
                        <li>Menyelenggarakan ibadah yang khusyuk dan nyaman.</li>
                        <li>Mengelola dana umat secara transparan dan akuntabel.</li>
                        <li>Memberdayakan ekonomi umat melalui program sosial.</li>
                    </ul>
                </div>
            </div>

            <!-- Sejarah -->
            <h2 class="text-3xl font-bold text-center text-slate-800 mb-8">Sejarah Singkat</h2>
            <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                <div v-for="(item, index) in histories" :key="index" class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <!-- Icon -->
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-emerald-100 group-[.is-active]:bg-emerald-500 text-emerald-500 group-[.is-active]:text-emerald-50 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                        <span class="text-xs font-bold text-white">{{ item.year }}</span>
                    </div>
                    <!-- Card -->
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-4 rounded-xl shadow-md border border-slate-100">
                        <h3 class="font-bold text-slate-800">{{ item.title }}</h3>
                        <p class="text-sm text-slate-600 mt-1">{{ item.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
