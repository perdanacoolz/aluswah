<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    committee: Object
});
</script>

<template>
    <Head title="Struktur Pengurus DKM" />

    <PublicLayout>

        <div class="bg-emerald-700 pt-32 pb-16 text-center text-white">
            <h1 class="text-4xl font-bold mb-4">Struktur Pengurus DKM</h1>
            <p class="text-emerald-100">Dewan Kemakmuran Masjid Al-Hidayah Periode 2024-2027</p>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-12">
            <!-- Reuse hierarchy layout similar to Welcome.vue but fuller -->
            <div class="flex flex-col items-center space-y-12">
                
                 <!-- Top: Ketua -->
                 <div v-if="committee.Inti" class="w-full max-w-sm">
                    <div v-for="member in committee.Inti.filter(m => m.order === 1)" :key="member.id" class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-8 border-emerald-600">
                        <div class="h-32 bg-emerald-50 relative">
                            <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                                <img :src="member.photo_url" :alt="member.name" class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover" />
                            </div>
                        </div>
                        <div class="pt-16 pb-6 px-6 text-center">
                            <h3 class="text-xl font-bold text-slate-900">{{ member.name }}</h3>
                            <p class="text-emerald-600 font-medium">{{ member.position }}</p>
                        </div>
                    </div>
                 </div>

                 <!-- Inti members (excluding order 1/Ketua) -->
                 <div v-if="committee.Inti" class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-3xl">
                    <div v-for="member in committee.Inti.filter(m => m.order > 1)" :key="member.id" class="bg-white rounded-xl shadow-lg p-6 border border-slate-100 flex items-center gap-4">
                        <img :src="member.photo_url" :alt="member.name" class="w-16 h-16 rounded-full border-2 border-slate-100 object-cover" />
                        <div>
                            <h4 class="font-bold text-slate-800">{{ member.name }}</h4>
                            <p class="text-emerald-600 text-sm font-medium">{{ member.position }}</p>
                        </div>
                    </div>
                 </div>

                 <!-- Divisions -->
                 <div class="w-full">
                    <h3 class="text-2xl font-bold text-center text-slate-800 mb-8">Bidang & Seksi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <template v-for="(members, divName) in committee" :key="divName">
                            <div v-if="divName !== 'Inti'" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-shadow">
                                <h4 class="font-bold text-emerald-700 mb-4 border-b border-slate-100 pb-2">{{ divName }}</h4>
                                <div class="space-y-4">
                                    <div v-for="member in members" :key="member.id" class="flex items-center gap-3">
                                        <img :src="member.photo_url" :alt="member.name" class="w-10 h-10 rounded-full bg-slate-100 object-cover" />
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ member.name }}</div>
                                            <div class="text-xs text-slate-500">{{ member.position }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                 </div>
            </div>
        </div>
    </PublicLayout>
</template>
