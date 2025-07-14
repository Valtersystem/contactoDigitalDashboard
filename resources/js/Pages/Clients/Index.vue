<script setup lang="ts">
import { ref } from 'vue'; // Importe o ref
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; // Importe useForm
import type { Client, PaginatedData } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue'; // Botão de perigo
import Modal from '@/Components/Modal.vue'; // Modal de confirmação
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps<{
    clients: PaginatedData<Client>;
}>();

// Estado para controlar a visibilidade do modal
const confirmingClientDeletion = ref(false);
// Ref para guardar o ID do cliente que queremos apagar
const clientToDeleteId = ref<number | null>(null);

// Formulário do Inertia para a ação de apagar
const form = useForm({});

// Função para abrir o modal de confirmação
const confirmClientDeletion = (id: number) => {
    clientToDeleteId.value = id; // Guardamos o ID na nossa ref
    confirmingClientDeletion.value = true;
};

// Função para apagar o cliente
const deleteClient = () => {
    // Verificamos se temos um ID antes de prosseguir
    if (clientToDeleteId.value === null) return;

    // Usamos o ID da nossa ref para construir a rota corretamente
    form.delete(route('clients.destroy', clientToDeleteId.value), {
        preserveScroll: true, // Melhora a experiência do utilizador
        onSuccess: () => closeModal(),
    });
};

// Função para fechar o modal
const closeModal = () => {
    confirmingClientDeletion.value = false;
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestão de Clientes
                </h2>
                <Link :href="route('clients.create')">
                    <PrimaryButton>Novo Cliente</PrimaryButton>
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome da Empresa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIF</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="client in clients.data" :key="client.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ client.business_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ client.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ client.nif }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link :href="route('clients.edit', client.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                        <Link :href="route('clients.show', client.id)" class="text-indigo-600 hover:text-indigo-900">Visualizar</Link>
                                        <!-- Botão para apagar que chama a função de confirmação -->
                                        <button @click="confirmClientDeletion(client.id)" class="text-red-600 hover:text-red-900">Apagar</button>
                                    </td>
                                </tr>
                                <tr v-if="clients.data.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum cliente encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação -->
        <Modal :show="confirmingClientDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Tem a certeza que quer apagar este cliente?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Uma vez apagado, todos os seus dados serão permanentemente removidos. Esta ação não pode ser desfeita.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>
                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteClient"
                    >
                        Apagar Cliente
                    </DangerButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
