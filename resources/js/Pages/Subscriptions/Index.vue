<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Subscription, PaginatedData, Client, Product } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineProps<{
    subscriptions: PaginatedData<Subscription & { client: Client, product: Product }>;
}>();

const form = useForm({});

const cancelSubscription = (subscriptionId: number) => {
    if (confirm('Tem a certeza de que quer cancelar esta assinatura? Esta ação não pode ser desfeita.')) {
        form.delete(route('subscriptions.destroy', subscriptionId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>

    <Head title="Assinaturas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestão de Assinaturas
                </h2>
                <Link :href="route('subscriptions.create')">
                <PrimaryButton>Nova Assinatura</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produto</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Próx. Faturação</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="subscription in subscriptions.data" :key="subscription.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ subscription.client.business_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ subscription.product.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ new
                                        Date(subscription.next_billing_date).toLocaleDateString() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': subscription.status === 'active',
                                                'bg-red-100 text-red-800': subscription.status === 'cancelled',
                                            }">
                                            {{ subscription.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link :href="route('subscriptions.edit', subscription.id)"
                                            class="text-indigo-600 hover:text-indigo-900">Editar</Link>

                                        <DangerButton @click="cancelSubscription(subscription.id)"
                                            v-if="subscription.status === 'active'">
                                            Cancelar
                                        </DangerButton>
                                    </td>
                                </tr>
                                <tr v-if="subscriptions.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhuma assinatura
                                        encontrada.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
