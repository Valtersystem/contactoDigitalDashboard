<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { Rental, PaginatedData } from '@/types';

const props = defineProps<{
    rentals: PaginatedData<Rental>;
}>();
</script>

<template>
    <Head title="Alugueis" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Histórico de Alugueis
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data do Aluguel
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="rental in rentals.data" :key="rental.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ rental.client?.business_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ new Date(rental.rental_date).toLocaleDateString() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                              :class="{
                                                  'bg-blue-100 text-blue-800': rental.status === 'Alugado',
                                                  'bg-yellow-100 text-yellow-800': rental.status === 'Atrasado',
                                                  'bg-gray-100 text-gray-800': rental.status === 'Devolvido'
                                              }">
                                            {{ rental.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link v-if="rental.status === 'Alugado' || rental.status === 'Atrasado'"
                                              :href="route('rentals.return.form', rental.id)"
                                              class="text-indigo-600 hover:text-indigo-900">
                                            Devolver
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="rentals.data.length === 0">
                                     <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum aluguel encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
