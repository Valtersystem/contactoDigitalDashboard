<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Category, PaginatedData } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps<{
    categories: PaginatedData<Category>;
}>();

const confirmingCategoryDeletion = ref(false);
const categoryToDeleteId = ref<number | null>(null);
const form = useForm({});

const confirmCategoryDeletion = (id: number) => {
    categoryToDeleteId.value = id;
    confirmingCategoryDeletion.value = true;
};

const deleteCategory = () => {
    if (categoryToDeleteId.value === null) return;
    form.delete(route('categories.destroy', categoryToDeleteId.value), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingCategoryDeletion.value = false;
};
</script>

<template>
    <Head title="Categorias" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestão de Categorias
                </h2>
                <Link :href="route('categories.create')">
                    <PrimaryButton>Nova Categoria</PrimaryButton>
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="category in categories.data" :key="category.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ category.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ category.slug }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link :href="route('categories.edit', category.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                        <button @click="confirmCategoryDeletion(category.id)" class="text-red-600 hover:text-red-900">Apagar</button>
                                    </td>
                                </tr>
                                <tr v-if="categories.data.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhuma categoria encontrada.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingCategoryDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tem a certeza que quer apagar esta categoria?</h2>
                <p class="mt-1 text-sm text-gray-600">Esta ação não pode ser desfeita.</p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>
                    <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteCategory">
                        Apagar Categoria
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
