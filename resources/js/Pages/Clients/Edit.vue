<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import type { Client } from '@/types'; // Importamos o nosso tipo Client

// Recebemos o cliente que o controller nos enviou como uma prop.
const props = defineProps<{
    client: Client;
}>();

// Inicializamos o formulário com os dados do cliente que recebemos.
const form = useForm({
    name: props.client.name,
    nif: props.client.nif,
    business_name: props.client.business_name,
    email: props.client.email || '',
    phone: props.client.phone || '',
    address: props.client.address || '',
});

const submit = () => {
    // Usamos o método PUT para atualizar, e passamos o ID do cliente na rota.
    form.put(route('clients.update', props.client.id));
};
</script>

<template>
    <Head title="Editar Cliente" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Cliente: {{ props.client.business_name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Nome da Empresa -->
                            <div>
                                <InputLabel for="business_name" value="Nome da Empresa" />
                                <TextInput id="business_name" type="text" class="mt-1 block w-full" v-model="form.business_name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.business_name" />
                            </div>

                            <!-- Nome do Contacto -->
                            <div>
                                <InputLabel for="name" value="Nome do Contacto" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- NIF -->
                            <div>
                                <InputLabel for="nif" value="NIF" />
                                <TextInput id="nif" type="text" class="mt-1 block w-full" v-model="form.nif" required />
                                <InputError class="mt-2" :message="form.errors.nif" />
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <InputLabel for="phone" value="Telefone" />
                                <TextInput id="phone" type="tel" class="mt-1 block w-full" v-model="form.phone" />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <!-- Endereço -->
                            <div>
                                <InputLabel for="address" value="Endereço" />
                                <TextInput id="address" type="text" class="mt-1 block w-full" v-model="form.address" />
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Salvar Alterações</PrimaryButton>
                                <Link :href="route('clients.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
