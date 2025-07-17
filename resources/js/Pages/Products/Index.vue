<script setup lang="ts">
import { ref, watch } from 'vue';
import { router, Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Product, PaginatedData } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ProductCard from '@/Components/ProductCard.vue';
import TextInput from '@/Components/TextInput.vue';
import { debounce } from 'lodash';

const props = defineProps<{
    products: PaginatedData<Product>;
    filters: { search: string | null };
}>();

const viewMode = ref<'grid' | 'list'>('grid');
const search = ref(props.filters.search);

// Observa o campo de pesquisa e aciona a busca com um "debounce"
watch(search, debounce((value: string | null) => {
    router.get(route('products.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

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
            <div class="flex justify-between items-center gap-4">
                <div class="flex-grow">
                     <TextInput v-model="search" type="text" class="block w-full" placeholder="Pesquisar por nome ou SKU..." />
                </div>
                <div class="flex items-center gap-4">
                     <div class="flex items-center gap-2">
                        <button @click="viewMode = 'grid'" :class="{'bg-gray-200': viewMode === 'grid'}" class="p-2 rounded-md text-gray-600 hover:bg-gray-100 transition">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        </button>
                         <button @click="viewMode = 'list'" :class="{'bg-gray-200': viewMode === 'list'}" class="p-2 rounded-md text-gray-600 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                    <Link :href="route('products.create')">
                        <PrimaryButton>Novo Produto</PrimaryButton>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                 <div v-if="viewMode === 'grid'" class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                    <ProductCard
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                        @delete="confirmProductDeletion"
                    />
                     <div v-if="products.data.length === 0" class="col-span-full text-center py-12 text-gray-500">
                        Nenhum produto encontrado.
                    </div>
                 </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
