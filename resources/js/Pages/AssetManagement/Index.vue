<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import type { Asset, PaginatedData, Product } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps<{
    assets: PaginatedData<Asset & { product: Product }>;
}>();

const updateStatus = (asset: Asset, newStatus: 'Disponível' | 'Em Manutenção' | 'Perdido') => {
    const form = useForm({
        status: newStatus,
    });
    if (confirm(`Tem a certeza que quer alterar o status para "${newStatus}"?`)) {
        form.patch(route('asset-management.updateStatus', asset.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Gestão de Ativos Danificados e Perdidos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestão de Ativos Danificados e Perdidos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº de Série</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Atual</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Rep.</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="asset in assets.data" :key="asset.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ asset.product.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono">{{ asset.serial_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                              :class="{ 'bg-yellow-100 text-yellow-800': asset.status === 'Em Manutenção', 'bg-red-100 text-red-800': asset.status === 'Perdido' }">
                                            {{ asset.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">€{{ asset.product.replacement_value }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="updateStatus(asset, 'Disponível')" class="text-green-600 hover:text-green-900">Marcar como Disponível</button>
                                        <button v-if="asset.status === 'Perdido'" @click="updateStatus(asset, 'Em Manutenção')" class="text-yellow-600 hover:text-yellow-900">Marcar como Em Manutenção</button>
                                        <button v-if="asset.status === 'Em Manutenção'" @click="updateStatus(asset, 'Perdido')" class="text-red-600 hover:text-red-900">Marcar como Perdido</button>
                                    </td>
                                </tr>
                                <tr v-if="assets.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum ativo danificado ou perdido encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
