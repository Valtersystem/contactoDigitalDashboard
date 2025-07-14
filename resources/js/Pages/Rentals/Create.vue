<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Client, Product, Asset } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

interface CartItem {
    product_id: number;
    name: string;
    tracking_type: 'BULK' | 'SERIALIZED';
    asset_id: number | null;
    serial_number?: string;
    quantity: number;
    stock?: number;
}
interface RentalForm {
    client_id: number | null;
    rental_date: string;
    expected_return_date: string;
    notes: string;
    items: CartItem[];
    [key: string]: any;
}

const props = defineProps<{
    clients: Client[];
    products: Product[];
}>();

const form = useForm<RentalForm>({
    client_id: null,
    rental_date: new Date().toISOString().slice(0, 10),
    expected_return_date: '',
    notes: '',
    items: [],
});

const errors = form.errors as Partial<Record<keyof RentalForm, string | string[]>>;


const selectedProduct = ref<Product | null>(null);
const selectedAsset = ref<number | null>(null);
const selectedQuantity = ref(1);

const availableProducts = computed(() => {
    const rentedAssetIds = new Set(
        form.items
            .map(item => item.asset_id)
            .filter((id): id is number => id !== null)
    );

    return props.products.map(p => {
        if (p.tracking_type === 'SERIALIZED') {
            return {
                ...p,
                assets: p.assets?.filter(a => a.status === 'Disponível' && !rentedAssetIds.has(a.id)),
            };
        }
        return p;
    }).filter(p => {
        if (p.tracking_type === 'SERIALIZED' && (!p.assets || p.assets.length === 0)) {
            return false;
        }
        if (p.tracking_type === 'BULK' && p.stock_quantity <= 0) {
            return false;
        }
        return true;
    });
});

watch(selectedProduct, () => {
    selectedAsset.value = null;
    selectedQuantity.value = 1;
});

function addProductToCart() {
    if (!selectedProduct.value) return;
    const product = selectedProduct.value;

    if (product.tracking_type === 'BULK') {
        const stock = product.stock_quantity ?? 0;
        if (selectedQuantity.value > stock) {
            alert("Quantidade solicitada excede o estoque disponível.");
            return;
        }

        const existingItem = form.items.find(item => item.product_id === product.id);
        if (existingItem) {
            existingItem.quantity += selectedQuantity.value;
        } else {
            form.items.push({
                product_id: product.id,
                name: product.name,
                tracking_type: 'BULK',
                asset_id: null,
                quantity: selectedQuantity.value,
                stock: product.stock_quantity
            });
        }
    } else {
        if (!selectedAsset.value) return;
        const asset = product.assets?.find(a => a.id === selectedAsset.value);
        if (asset) {
            if (form.items.some(item => item.asset_id === asset.id)) {
                alert("Este item já foi adicionado ao carrinho.");
                return;
            }
            form.items.push({
                product_id: product.id,
                name: product.name,
                tracking_type: 'SERIALIZED',
                asset_id: asset.id,
                serial_number: asset.serial_number,
                quantity: 1,
            });
        }
    }
    selectedProduct.value = null;
}

function removeItemFromCart(index: number) {
    form.items.splice(index, 1);
}

const submit = () => {
    form.post(route('rentals.store'));
};
</script>

<template>
    <Head title="Novo Aluguel" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registar Novo Aluguel
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Aluguel</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <InputLabel for="client_id" value="Cliente" />
                                <select id="client_id" v-model="form.client_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option :value="null" disabled>Selecione um cliente</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.business_name }}</option>
                                </select>
                                <InputError class="mt-2" :message="(form.errors as Record<keyof RentalForm, string[]>).client_id?.[0]" />
                            </div>
                            <div>
                                <InputLabel for="rental_date" value="Data de Entrega" />
                                <TextInput id="rental_date" type="date" class="mt-1 block w-full" v-model="form.rental_date" required />
                                <InputError class="mt-2" :message="errors.expected_return_date?.[0]" />
                            </div>
                            <div>
                                <InputLabel for="expected_return_date" value="Data de Devolução Prevista" />
                                <TextInput id="expected_return_date" type="date" class="mt-1 block w-full" v-model="form.expected_return_date" required />
                                <InputError class="mt-2" :message="errors.rental_date?.[0]" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <InputLabel for="notes" value="Observações" />
                            <textarea id="notes" v-model="form.notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Itens ao Aluguel</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-2">
                                <InputLabel for="product_selection" value="Produto" />
                                <select id="product_selection" v-model="selectedProduct" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option :value="null" disabled>Selecione um produto</option>
                                    <option v-for="product in availableProducts" :key="product.id" :value="product">{{ product.name }}</option>
                                </select>
                            </div>

                            <div v-if="selectedProduct?.tracking_type === 'BULK'">
                                <InputLabel for="quantity" value="Quantidade" />
                                <TextInput id="quantity" type="number" min="1" :max="selectedProduct.stock_quantity" class="mt-1 block w-full" v-model="selectedQuantity" />
                            </div>

                            <div v-if="selectedProduct?.tracking_type === 'SERIALIZED'">
                                <InputLabel for="asset_selection" value="Número de Série" />
                                <select id="asset_selection" v-model="selectedAsset" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option :value="null" disabled>Selecione um ativo</option>
                                    <option v-for="asset in selectedProduct.assets" :key="asset.id" :value="asset.id">{{ asset.serial_number }}</option>
                                </select>
                            </div>

                            <div>
                                <PrimaryButton @click="addProductToCart" type="button" class="w-full justify-center">Adicionar</PrimaryButton>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                         <h3 class="text-lg font-medium text-gray-900 mb-4">Itens do Aluguel</h3>
                         <div class="border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalhe</th>
                                        <th class="relative px-6 py-3"><span class="sr-only">Remover</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(item, index) in form.items" :key="index">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ item.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="item.tracking_type === 'BULK'">Quantidade: {{ item.quantity }}</span>
                                            <span v-else class="font-mono">{{ item.serial_number }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button @click="removeItemFromCart(index)" type="button" class="text-red-600 hover:text-red-900">Remover</button>
                                        </td>
                                    </tr>
                                    <tr v-if="form.items.length === 0">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhum item adicionado.</td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>
                         <InputError class="mt-2" :message="errors.items?.[0]" />
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton :disabled="form.processing || form.items.length === 0">
                            Registar Aluguel
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
