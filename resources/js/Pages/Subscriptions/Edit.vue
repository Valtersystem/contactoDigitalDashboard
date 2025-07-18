<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import type { Subscription, Product, Client } from '@/types';

const props = defineProps<{
    subscription: Subscription & { client: Client, product: Product };
    products: Product[];
}>();

const form = useForm({
    product_id: props.subscription.product_id,
    next_billing_date: props.subscription.next_billing_date.slice(0, 10), // Formatar a data para YYYY-MM-DD
    license_key: props.subscription.license_key || '',
    status: props.subscription.status,
});

const submit = () => {
    form.put(route('subscriptions.update', props.subscription.id));
};
</script>

<template>
    <Head :title="`Editar Assinatura #${subscription.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Assinatura #{{ subscription.id }} - {{ subscription.client.business_name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-lg mx-auto">
                            <div>
                                <InputLabel for="product_id" value="Produto de Licença" />
                                <select id="product_id" v-model="form.product_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }} ({{ product.replacement_value }}€/mês)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.product_id" />
                            </div>

                            <div>
                                <InputLabel for="next_billing_date" value="Próxima Data de Faturação" />
                                <TextInput id="next_billing_date" type="date" class="mt-1 block w-full" v-model="form.next_billing_date" required />
                                <InputError class="mt-2" :message="form.errors.next_billing_date" />
                            </div>

                             <div>
                                <InputLabel for="status" value="Status" />
                                <select id="status" v-model="form.status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="active">Ativa</option>
                                    <option value="unpaid">Não Paga</option>
                                    <option value="cancelled">Cancelada</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div>
                                <InputLabel for="license_key" value="Chave da Licença (Opcional)" />
                                <TextInput id="license_key" type="text" class="mt-1 block w-full" v-model="form.license_key" />
                                <InputError class="mt-2" :message="form.errors.license_key" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Salvar Alterações</PrimaryButton>
                                <Link :href="route('subscriptions.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
