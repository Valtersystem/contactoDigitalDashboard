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

const props = defineProps<{
    product: Product;
    assets: PaginatedData<Asset>;
}>();

const confirmingAssetDeletion = ref(false);
const assetToDeleteId = ref<number | null>(null);

const form = useForm({
    serial_number: '',
});

const assetDeleteForm = useForm({});

const addAsset = () => {
    form.post(route('products.assets.store', props.product.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const confirmAssetDeletion = (id: number) => {
    assetToDeleteId.value = id;
    confirmingAssetDeletion.value = true;
};

const deleteAsset = () => {
    if (assetToDeleteId.value === null) return;
    assetDeleteForm.delete(route('assets.destroy', assetToDeleteId.value), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
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
                <!-- Formulário para Adicionar Novo Ativo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Adicionar Novo Ativo</h3>
                        <form @submit.prevent="addAsset" class="mt-4 flex items-start gap-4">
                            <div class="flex-grow">
                                <InputLabel for="serial_number" value="Número de Série" class="sr-only" />
                                <TextInput id="serial_number" type="text" class="block w-full" v-model="form.serial_number" placeholder="Insira o número de série da etiqueta" required />
                                <InputError class="mt-2" :message="form.errors.serial_number" />
                            </div>
                            <PrimaryButton :disabled="form.processing">Adicionar</PrimaryButton>
                        </form>
                    </div>
                </div>

                <!-- Lista de Ativos Existentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ativos Registados</h3>
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
                                              :class="{ 'bg-green-100 text-green-800': asset.status === 'Disponível', 'bg-yellow-100 text-yellow-800': asset.status === 'Alugado' }">
                                            {{ asset.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="confirmAssetDeletion(asset.id)" class="text-red-600 hover:text-red-900">Apagar</button>
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
