<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { Rental } from '@/types';
import StatCard from '@/Components/StatCard.vue';

const props = defineProps<{
    stats: {
        totalClients: number;
        totalProducts: number;
        totalItemsRented: number;
        lateRentalsCount: number;
    };
    latestRentals: Rental[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard title="Total de Clientes" :value="stats.totalClients" :link="route('clients.index')" />
                    <StatCard title="Produtos Ativos" :value="stats.totalProducts" :link="route('products.index')" />
                    <StatCard title="Itens Alugados" :value="stats.totalItemsRented" :link="route('rentals.index')" />
                    <Link :href="route('rentals.index')" class="block p-6 transition bg-white shadow-sm hover:bg-gray-50 sm:rounded-lg" :class="{ '!bg-red-50 border border-red-200': stats.lateRentalsCount > 0 }">
                        <h3 class="text-sm font-medium text-gray-500 truncate">Aluguéis Atrasados</h3>
                        <p class="mt-1 text-3xl font-semibold" :class="{ 'text-red-600': stats.lateRentalsCount > 0, 'text-gray-900': stats.lateRentalsCount === 0 }">
                            {{ stats.lateRentalsCount }}
                        </p>
                    </Link>
                </div>
                </div>
        </div>
    </AuthenticatedLayout>
</template>
