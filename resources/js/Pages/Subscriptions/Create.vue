<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import type { Client, Product } from '@/types';

defineProps<{
    clients: Client[];
    products: Product[];
}>();

const form = useForm({
    client_id: null,
    product_id: null,
    start_date: new Date().toISOString().slice(0, 10),
    license_key: '',
});

const submit = () => {
    form.post(route('subscriptions.store'));
};
</script>

<template>
    <Head title="Nova Assinatura" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Criar Nova Assinatura
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-lg mx-auto">
                            <div>
                                <InputLabel for="client_id" value="Cliente" />
                                <select id="client_id" v-model="form.client_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option :value="null" disabled>Selecione um cliente</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.business_name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.client_id" />
                            </div>

                            <div>
                                <InputLabel for="product_id" value="Produto de Licença" />
                                <select id="product_id" v-model="form.product_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option :value="null" disabled>Selecione um produto</option>
                                    <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }} ({{ product.replacement_value }}€/mês)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.product_id" />
                            </div>

                            <div>
                                <InputLabel for="start_date" value="Data de Início" />
                                <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>

                            <div>
                                <InputLabel for="license_key" value="Chave da Licença (Opcional)" />
                                <TextInput id="license_key" type="text" class="mt-1 block w-full" v-model="form.license_key" />
                                <InputError class="mt-2" :message="form.errors.license_key" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Criar Assinatura</PrimaryButton>
                                <Link :href="route('subscriptions.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
