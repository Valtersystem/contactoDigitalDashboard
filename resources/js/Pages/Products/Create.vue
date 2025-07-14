<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import type { Category } from '@/types';

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    name: '',
    sku: '',
    description: '',
    category_id: null,
    tracking_type: 'BULK',
    stock_quantity: 0,
    replacement_value: 0,
    is_active: true,
    image: null as File | null,
});

const submit = () => {
    if (form.tracking_type !== 'BULK') {
        form.stock_quantity = 0;
    }
    form.post(route('products.store'));
};

function handleImageUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.image = target.files[0];
    }
}
</script>

<template>

    <Head title="Novo Produto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Criar Novo Produto
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="name" value="Nome do Produto" />
                                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                        required autofocus />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>
                                <div>
                                    <InputLabel for="sku" value="SKU (Código do Modelo)" />
                                    <TextInput id="sku" type="text" class="mt-1 block w-full" v-model="form.sku"
                                        required />
                                    <InputError class="mt-2" :message="form.errors.sku" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="description" value="Descrição" />
                                <textarea id="description" v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="image" value="Imagem do Produto" />
                                <input id="image" type="file" @change="handleImageUpload" class="mt-1 block w-full" />
                                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                    {{ form.progress.percentage }}%
                                </progress>
                                <InputError class="mt-2" :message="form.errors.image" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="category_id" value="Categoria" />
                                    <select id="category_id" v-model="form.category_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option :value="null" disabled>Selecione uma categoria</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.category_id" />
                                </div>
                                <div>
                                    <InputLabel for="replacement_value" value="Valor de Reposição (€)" />
                                    <TextInput id="replacement_value" type="number" step="0.01"
                                        class="mt-1 block w-full" v-model="form.replacement_value" required />
                                    <InputError class="mt-2" :message="form.errors.replacement_value" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                                <div>
                                    <InputLabel value="Tipo de Rastreamento" />
                                    <div class="mt-2 space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.tracking_type" value="BULK"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="ms-2">Em Massa (Quantidade)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.tracking_type" value="SERIALIZED"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="ms-2">Por Número de Série</span>
                                        </label>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.tracking_type" />
                                </div>

                                <div v-if="form.tracking_type === 'BULK'">
                                    <InputLabel for="stock_quantity" value="Quantidade em Estoque" />
                                    <TextInput id="stock_quantity" type="number" class="mt-1 block w-full"
                                        v-model="form.stock_quantity" required />
                                    <InputError class="mt-2" :message="form.errors.stock_quantity" />
                                </div>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <Checkbox v-model:checked="form.is_active" />
                                    <span class="ms-2 text-sm text-gray-600">Produto Ativo</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>
                                <Link :href="route('products.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
