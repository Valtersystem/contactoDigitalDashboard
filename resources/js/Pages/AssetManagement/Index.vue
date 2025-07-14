<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import type { Asset, PaginatedData, Product, Rental, RentalItem } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';


const props = defineProps<{
    assets: PaginatedData<Asset & { product: Product }>;
    bulkLosses: PaginatedData<RentalItem & { product: Product, rental: Rental & { client: { business_name: string } } }>;
}>();

// --- Lógica para Ativos Serializados ---
const updateStatus = (asset: Asset, newStatus: 'Disponível' | 'Em Manutenção' | 'Perdido') => {
    const form = useForm({ status: newStatus });
    if (confirm(`Tem a certeza de que quer alterar o status para "${newStatus}"?`)) {
        form.patch(route('asset-management.updateStatus', asset.id), {
            preserveScroll: true,
        });
    }
};

// --- NOVA Lógica para Itens em Massa ---
const showBulkActionModal = ref(false);
const selectedRentalItem = ref<RentalItem | null>(null);
const selectedAction = ref('');
const modalTitle = ref('');

const bulkActionForm = useForm({
    quantity: 1,
    action: '',
});

const openBulkActionModal = (item: RentalItem, action: string) => {
    selectedRentalItem.value = item;
    selectedAction.value = action;
    bulkActionForm.reset();
    bulkActionForm.action = action;

    if (action === 'damaged_to_available') modalTitle.value = 'Converter Danificado para Disponível';
    if (action === 'lost_to_available') modalTitle.value = 'Converter Perdido para Disponível';
    if (action === 'damaged_to_lost') modalTitle.value = 'Converter Danificado para Perdido';

    showBulkActionModal.value = true;
};

const executeBulkAction = () => {
    if (!selectedRentalItem.value) return;
    bulkActionForm.patch(route('asset-management.bulk.update', selectedRentalItem.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    showBulkActionModal.value = false;
    bulkActionForm.reset();
    bulkActionForm.clearErrors();
    selectedRentalItem.value = null;
    selectedAction.value = '';
    modalTitle.value = '';
};

const getMaxQuantityForAction = (item: RentalItem, action: string) => {
    if (action.startsWith('damaged')) return item.quantity_damaged;
    if (action.startsWith('lost')) return item.quantity_lost;
    return 1;
};

</script>

<template>

    <Head title="Gestão de Perdas e Danos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestão de Perdas e Danos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ativos Serializados (com N/S)</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produto</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nº de Série</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Atual</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Valor Rep.</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="asset in assets.data" :key="asset.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ asset.product.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono">{{ asset.serial_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{ 'bg-yellow-100 text-yellow-800': asset.status === 'Em Manutenção', 'bg-red-100 text-red-800': asset.status === 'Perdido' }">
                                            {{ asset.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">€{{ asset.product.replacement_value }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="updateStatus(asset, 'Disponível')"
                                            class="text-green-600 hover:text-green-900">Marcar como Disponível</button>
                                        <button v-if="asset.status === 'Perdido'"
                                            @click="updateStatus(asset, 'Em Manutenção')"
                                            class="text-yellow-600 hover:text-yellow-900">Marcar como Em
                                            Manutenção</button>
                                        <button v-if="asset.status === 'Em Manutenção'"
                                            @click="updateStatus(asset, 'Perdido')"
                                            class="text-red-600 hover:text-red-900">Marcar como Perdido</button>
                                    </td>
                                </tr>
                                <tr v-if="assets.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum ativo danificado
                                        ou
                                        perdido encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Danos e Perdas de Produtos em Massa</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produto</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aluguel</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Qtd. Danificada</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Qtd. Perdida</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prejuízo</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="loss in bulkLosses.data" :key="loss.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ loss.product.name }}
                                        <span class="block text-xs">{{ loss.product.sku }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        #{{ loss.rental.id }} ({{ loss.rental.client.business_name }})
                                        <span class="block text-xs">{{ new
                                            Date(loss.rental.rental_date).toLocaleDateString()
                                        }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ loss.quantity_damaged }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ loss.quantity_lost }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">€{{ (loss.product.replacement_value *
                                        (loss.quantity_damaged + loss.quantity_lost)).toFixed(2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Dropdown v-if="loss.quantity_damaged > 0 || loss.quantity_lost > 0" align="top"
                                            width="48">
                                            <template #trigger>
                                                <button
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                    Ações
                                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </template>
                                            <template #content>
                                                <button v-if="loss.quantity_damaged > 0"
                                                    @click="openBulkActionModal(loss, 'damaged_to_available')"
                                                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Mover
                                                    para Disponível</button>
                                                <button v-if="loss.quantity_lost > 0"
                                                    @click="openBulkActionModal(loss, 'lost_to_available')"
                                                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Mover
                                                    para Disponível <br> (Encontrado)</button>
                                                <button v-if="loss.quantity_damaged > 0"
                                                    @click="openBulkActionModal(loss, 'damaged_to_lost')"
                                                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Mover
                                                    para Perdido</button>
                                            </template>
                                        </Dropdown>
                                    </td>
                                </tr>
                                <tr v-if="bulkLosses.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhuma perda ou dano
                                        registado
                                        para produtos em massa.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showBulkActionModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ modalTitle }}</h2>
                <p v-if="selectedRentalItem" class="mt-1 text-sm text-gray-600">
                    Produto: {{ selectedRentalItem.product.name }}
                </p>
                <form @submit.prevent="executeBulkAction" class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="quantity" value="Quantidade" />
                        <TextInput id="quantity" type="number" class="mt-1 block w-full"
                            v-model="bulkActionForm.quantity" required
                            :max="selectedRentalItem ? getMaxQuantityForAction(selectedRentalItem, selectedAction) : 1"
                            min="1" />
                        <InputError class="mt-2" :message="bulkActionForm.errors.quantity" />
                    </div>

                    <div class="flex justify-end gap-4">
                        <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>
                        <PrimaryButton :disabled="bulkActionForm.processing">Confirmar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
