<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Rental } from '@/types';
import StatCard from '@/Components/StatCard.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps<{
    stats: {
        totalClients: number;
        totalProducts: number;
        lateRentalsCount: number;
        totalLossCount: number;
        productsWithoutPrice: number;
        totalInventoryValue: string;
        totalDamagedValue: string;
        totalLostValue: string;
    };
    upcomingReturns: Rental[];
}>();

const search = ref('');

const performSearch = () => {
    if (search.value.trim()) {
        router.get(route('products.index', { search: search.value }));
    }
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                 <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                       Olá, {{ $page.props.auth.user.name }}!
                    </h2>
                    <p class="text-sm text-gray-500">Aqui está um resumo da sua atividade.</p>
                 </div>
                 <form @submit.prevent="performSearch" class="w-full md:w-1/3">
                    <TextInput v-model="search" type="text" class="block w-full" placeholder="Pesquisar produtos..." />
                 </form>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Resumo Geral e Inventário</h3>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                       <StatCard title="Total de Clientes" :value="stats.totalClients" :link="route('clients.index')" />
                       <StatCard title="Quantidade de Produtos" :value="stats.totalProducts" :link="route('products.index')" />
                       <StatCard title="Quebras e Perdas (Qtd)" :value="stats.totalLossCount" :link="route('asset-management.index')" variant="warning" />
                       <Link :href="route('rentals.index', { 'status': 'Atrasado' })" class="block p-6 transition bg-white shadow-sm hover:bg-gray-50 sm:rounded-lg border" :class="{ '!bg-red-50 border border-red-200': stats.lateRentalsCount > 0 }">
                           <h3 class="text-sm font-medium text-gray-500 truncate">Aluguéis Atrasados</h3>
                           <p class="mt-1 text-3xl font-semibold" :class="{ 'text-red-600': stats.lateRentalsCount > 0, 'text-gray-900': stats.lateRentalsCount === 0 }">
                               {{ stats.lateRentalsCount }}
                           </p>
                       </Link>
                       <StatCard title="Valor Total em Produtos" :value="`€ ${stats.totalInventoryValue}`" :link="route('products.index')" />
                       <StatCard title="Valor em Produtos Danificados" :value="`€ ${stats.totalDamagedValue ?? '0,00'}`" :link="route('asset-management.index')" variant="warning" />
                       <StatCard title="Valor em Produtos Perdidos" :value="`€ ${stats.totalLostValue ?? '0,00'}`" :link="route('asset-management.index')" variant="danger" />
                       <StatCard title="Produtos sem Preço" :value="stats.productsWithoutPrice" :link="route('products.index', { sem_preco: true })" :variant="stats.productsWithoutPrice > 0 ? 'danger' : 'default'" />
                   </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Próximos Retornos</h3>
                    <div class="space-y-4">
                         <div v-if="upcomingReturns.length === 0" class="text-center text-sm text-gray-500 py-4">
                            Nenhum retorno agendado.
                        </div>
                        <div v-for="rental in upcomingReturns" :key="rental.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ rental.client?.business_name }}</p>
                                <p class="text-xs text-gray-500">Aluguel #{{ rental.id }}</p>
                            </div>
                            <p class="text-sm font-medium text-primary">{{ new Date(rental.expected_return_date).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
