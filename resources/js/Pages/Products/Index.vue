<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Product, PaginatedData } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps<{
    products: PaginatedData<Product>;
}>();

const confirmingProductDeletion = ref(false);
const productToDeleteId = ref<number | null>(null);
const form = useForm({});

const confirmProductDeletion = (id: number) => {
    productToDeleteId.value = id;
    confirmingProductDeletion.value = true;
};

const deleteProduct = () => {
    if (productToDeleteId.value === null) return;
    form.delete(route('products.destroy', productToDeleteId.value), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingProductDeletion.value = false;
};
</script>

<template>

    <Head title="Produtos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestão de Produtos
                </h2>
                <Link :href="route('products.create')">
                <PrimaryButton>Novo Produto</PrimaryButton>
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
                                        Imagem
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Categoria</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estoque</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="product in products.data" :key="product.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img v-if="product.image_url" :src="`/storage/${product.image_url}`"
                                            :alt="product.name" class="h-10 w-10 rounded-full object-cover">
                                        <span v-else
                                            class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">Sem
                                            Foto</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ product.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ product.category?.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template v-if="product.tracking_type === 'SERIALIZED'">
                                            {{ product.assets_count }}
                                        </template>
                                        <template v-else>
                                            {{ product.stock_quantity }}
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ product.is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link :href="route('products.edit', product.id)"
                                            class="text-indigo-600 hover:text-indigo-900">Editar
                                        </Link>
                                        <button @click="confirmProductDeletion(product.id)"
                                            class="text-red-600 hover:text-red-900">Apagar
                                        </button>
                                        <Link v-if="product.tracking_type === 'SERIALIZED'"
                                            :href="route('products.assets.index', product.id)"
                                            class="text-blue-600 hover:text-blue-900">
                                        Gerir Ativos
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum produto
                                        encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingProductDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tem a certeza que quer apagar este produto?</h2>
                <p class="mt-1 text-sm text-gray-600">Esta ação não pode ser desfeita.</p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>
                    <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteProduct">
                        Apagar Produto
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
