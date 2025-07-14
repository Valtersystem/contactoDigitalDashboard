<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Client, Rental, Product, Asset, RentalItem } from '@/types';

const props = defineProps<{
    client: Client;
    stats: {
        total_rentals: number;
        active_rentals: number;
        completed_rentals: number;
    };
}>();

// A página de devolução já existe e funciona bem.
// Vamos simplesmente redirecionar para ela, o que é mais limpo e simples.
// Não precisamos mais de lógica de modal ou de um segundo formulário aqui.
</script>

<template>
    <Head :title="`Perfil de ${client.business_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Perfil do Cliente
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex flex-col items-center">
                            <img class="h-24 w-24 rounded-full object-cover" :src="client.photo_url ? `/storage/${client.photo_url}` : `https://ui-avatars.com/api/?name=${client.name}&background=e8f0fe&color=3730a3`" :alt="client.name">
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ client.business_name }}</h3>
                            <p class="text-sm text-gray-500">{{ client.name }}</p>
                            <Link :href="route('clients.edit', client.id)" class="mt-4 inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Editar Perfil
                            </Link>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="text-base font-medium text-gray-800">Informações de Contacto</h4>
                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="text-gray-900">{{ client.email || 'N/A' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Telefone</dt><dd class="text-gray-900">{{ client.phone || 'N/A' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">NIF</dt><dd class="text-gray-900">{{ client.nif }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Endereço</dt><dd class="text-gray-900 text-right">{{ client.address || 'N/A' }}</dd></div>
                        </dl>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                         <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                            <p class="text-sm text-gray-500">Total de Aluguéis</p>
                            <p class="text-3xl font-semibold text-gray-900">{{ stats.total_rentals }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                            <p class="text-sm text-gray-500">Aluguéis Ativos</p>
                            <p class="text-3xl font-semibold text-primary">{{ stats.active_rentals }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                            <p class="text-sm text-gray-500">Aluguéis Concluídos</p>
                            <p class="text-3xl font-semibold text-green-600">{{ stats.completed_rentals }}</p>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900">Histórico de Aluguéis</h3>
                            <div class="mt-4 space-y-4">
                                <div v-if="!client.rentals || client.rentals.length === 0" class="text-center text-gray-500 py-4">
                                    Este cliente ainda não tem aluguéis registados.
                                </div>
                                <div v-for="rental in client.rentals" :key="rental.id" class="border rounded-lg p-4">
                                     <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-indigo-700">Aluguel #{{ rental.id }}</h4>
                                            <p class="text-sm text-gray-600">
                                                De {{ new Date(rental.rental_date).toLocaleDateString() }} até {{ new Date(rental.expected_return_date).toLocaleDateString() }}
                                            </p>
                                             <ul class="mt-4 border-t border-gray-200 pt-2 text-sm">
                                                <li v-for="item in rental.rental_items" :key="item.id" class="flex items-center gap-4">
                                                    <span class="font-mono text-gray-500 w-28 flex-shrink-0">
                                                        <template v-if="item.product.tracking_type === 'BULK'">x{{ item.quantity_rented }}</template>
                                                        <template v-else>{{ item.asset?.serial_number }}</template>
                                                    </span>
                                                    <span class="text-gray-800">{{ item.product.name }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="{ 'bg-blue-100 text-blue-800': rental.status === 'Alugado', 'bg-yellow-100 text-yellow-800': rental.status === 'Atrasado', 'bg-green-100 text-green-800': rental.status === 'Devolvido' }">
                                                {{ rental.status }}
                                            </span>
                                            <Link v-if="rental.status === 'Alugado' || rental.status === 'Atrasado'"
                                                  :href="route('rentals.return.form', rental.id)"
                                                  class="mt-2 block w-full text-center text-sm text-white bg-primary hover:bg-primary-light rounded-md px-3 py-1 transition-colors">
                                                Devolver
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
