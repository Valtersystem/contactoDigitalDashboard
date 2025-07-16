<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Asset, Product, PaginatedData } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
import Swal from 'sweetalert2';

const props = defineProps<{
    product: Product;
    assets: PaginatedData<Asset>;
}>();

// --- Lógica para Adicionar Ativo ---
const addAssetForm = useForm({
    serial_number: '',
});

const addAsset = () => {
    addAssetForm.post(route('products.assets.store', props.product.id), {
        preserveScroll: true,
        onSuccess: () => addAssetForm.reset(),
    });
};

// --- Lógica para Apagar Ativo ---
const confirmingAssetDeletion = ref(false);
const assetToDelete = ref<Asset | null>(null);
const assetDeleteForm = useForm({});

const confirmAssetDeletion = (asset: Asset) => {
    if (asset.status === 'Alugado') {
        Swal.fire('Ação Bloqueada', 'Não é possível apagar um ativo que está atualmente alugado.', 'error');
        return;
    }
    assetToDelete.value = asset;
    confirmingAssetDeletion.value = true;
};

const deleteAsset = () => {
    if (!assetToDelete.value) return;
    assetDeleteForm.delete(route('assets.destroy', assetToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

// --- Lógica para Atualizar Status ---
const updateStatusForm = useForm({
    status: '',
});

const updateStatus = (asset: Asset, newStatus: string) => {
    if (asset.status === 'Alugado') {
        Swal.fire('Ação Bloqueada', 'Não é possível alterar o status de um ativo que está atualmente alugado.', 'error');
        return;
    }

    updateStatusForm.status = newStatus;
    Swal.fire({
        title: `Alterar para "${newStatus}"?`,
        text: `Tem a certeza de que quer alterar o status do ativo ${asset.serial_number}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, alterar!',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            updateStatusForm.patch(route('assets.updateStatus', asset.id), {
                preserveScroll: true
            });
        }
    });
};

const closeModal = () => {
    confirmingAssetDeletion.value = false;
};
</script>

<template>
    <Head :title="`Ativos de ${product.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gerir Ativos para: <span class="font-bold">{{ product.name }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Adicionar Novo Ativo</h3>
                        <form @submit.prevent="addAsset" class="mt-4 flex items-start gap-4">
                            <div class="flex-grow">
                                <InputLabel for="serial_number" value="Número de Série" class="sr-only" />
                                <TextInput id="serial_number" type="text" class="block w-full" v-model="addAssetForm.serial_number" placeholder="Insira o número de série da etiqueta" required />
                                <InputError class="mt-2" :message="addAssetForm.errors.serial_number" />
                            </div>
                            <PrimaryButton :disabled="addAssetForm.processing">Adicionar</PrimaryButton>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ativos Registados</h3>
                        <div class="border rounded-lg overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Série</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="asset in assets.data" :key="asset.id">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono">{{ asset.serial_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="{
                                                    'bg-green-100 text-green-800': asset.status === 'Disponível',
                                                    'bg-blue-100 text-blue-800': asset.status === 'Alugado',
                                                    'bg-yellow-100 text-yellow-800': asset.status === 'Em Manutenção',
                                                    'bg-red-100 text-red-800': asset.status === 'Perdido',
                                                    }">
                                                {{ asset.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Dropdown align="right" width="48">
                                                <template #trigger>
                                                    <button :disabled="asset.status === 'Alugado'" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        Ações
                                                    </button>
                                                </template>
                                                <template #content>
                                                    <button v-if="asset.status !== 'Disponível'" @click="updateStatus(asset, 'Disponível')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100">Marcar como Disponível</button>
                                                    <button v-if="asset.status !== 'Em Manutenção'" @click="updateStatus(asset, 'Em Manutenção')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100">Marcar como Em Manutenção</button>
                                                    <button v-if="asset.status !== 'Perdido'" @click="updateStatus(asset, 'Perdido')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100">Marcar como Perdido</button>
                                                    <div class="border-t border-gray-100"></div>
                                                    <button @click="confirmAssetDeletion(asset)" class="block w-full px-4 py-2 text-left text-sm leading-5 text-red-600 hover:bg-gray-100">Apagar</button>
                                                </template>
                                            </Dropdown>
                                        </td>
                                    </tr>
                                    <tr v-if="assets.data.length === 0">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhum ativo registado para este produto.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingAssetDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tem a certeza que quer apagar este ativo?</h2>
                <p class="mt-1 text-sm text-gray-600">Esta ação não pode ser desfeita.</p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>
                    <DangerButton class="ms-3" :class="{ 'opacity-25': assetDeleteForm.processing }" :disabled="assetDeleteForm.processing" @click="deleteAsset">
                        Apagar Ativo
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
