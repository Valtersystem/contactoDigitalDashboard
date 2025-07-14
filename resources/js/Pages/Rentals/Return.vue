<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import type { Rental, RentalItem, Product, Asset } from '@/types';

// Interfaces (sem alterações)
interface ReturnItemForm {
    rental_item_id: number;
    quantity_returned: number;
    quantity_damaged: number;
    quantity_lost: number;
}
interface ReturnForm {
    actual_return_date: string;
    notes: string;
    items: ReturnItemForm[];
    [key: string]: any;
}

const props = defineProps<{
    rental: Rental & { rental_items: (RentalItem & { product: Product, asset?: Asset })[] };
}>();

const form = useForm<ReturnForm>({
    actual_return_date: new Date().toISOString().slice(0, 10),
    notes: '',
    items: props.rental.rental_items.map(item => ({
        rental_item_id: item.id,
        quantity_returned: item.product.tracking_type === 'BULK' ? (item.quantity_rented ?? 0) : 1,
        quantity_damaged: 0,
        quantity_lost: 0,
    })),
});

const submit = () => {
    form.post(route('rentals.return.process', props.rental.id), {
        preserveScroll: true,
    });
};

const getMaxQuantity = (item: RentalItem) => {
    return item.quantity_rented ?? 1;
};

// --- FUNÇÃO DE LÓGICA ATUALIZADA ---
function updateQuantities(index: number, changedField: 'returned' | 'damaged' | 'lost') {
    const originalItem = props.rental.rental_items[index];
    const formItem = form.items[index];

    const totalRented = originalItem.quantity_rented ?? 1;
    let returned = Number(formItem.quantity_returned) || 0;
    let damaged = Number(formItem.quantity_damaged) || 0;
    let lost = Number(formItem.quantity_lost) || 0;

    // Garante que os valores não são negativos
    if (returned < 0) { formItem.quantity_returned = 0; returned = 0; }
    if (damaged < 0) { formItem.quantity_damaged = 0; damaged = 0; }
    if (lost < 0) { formItem.quantity_lost = 0; lost = 0; }

    const totalEntered = returned + damaged + lost;

    if (totalEntered > totalRented) {
        const overage = totalEntered - totalRented;
        // Reduz o valor do campo que acabou de ser alterado para que a soma não ultrapasse o total alugado
        if (changedField === 'returned') {
            formItem.quantity_returned -= overage;
        } else if (changedField === 'damaged') {
            formItem.quantity_damaged -= overage;
        } else if (changedField === 'lost') {
            formItem.quantity_lost -= overage;
        }
    }
}
</script>

<template>
    <Head :title="`Devolução do Aluguel #${rental.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registar Devolução do Aluguel #{{ rental.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Cliente: {{ rental.client?.business_name }}</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Data do Aluguel: {{ new Date(rental.rental_date).toLocaleDateString() }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Devolução Prevista: {{ new Date(rental.expected_return_date).toLocaleDateString() }}
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <InputLabel for="actual_return_date" value="Data da Devolução Efetiva" />
                            <TextInput id="actual_return_date" type="date" class="mt-1 block w-full" v-model="form.actual_return_date" required />
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Itens a Devolver</h3>
                            <div class="border rounded-lg overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Detalhe</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Devolvido</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Danificado</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perdido</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="(item, index) in rental.rental_items" :key="item.id">
                                            <td class="px-4 py-2 whitespace-nowrap">{{ item.product.name }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap font-mono text-sm">
                                                <span v-if="item.product.tracking_type === 'BULK'">Qtd: {{ item.quantity_rented }}</span>
                                                <span v-else>{{ item.asset?.serial_number }}</span>
                                            </td>
                                            <td>
                                                <TextInput type="number" class="w-20" min="0" :max="getMaxQuantity(item)" v-model="form.items[index].quantity_returned" @input="updateQuantities(index, 'returned')" />
                                            </td>
                                            <td>
                                                <TextInput type="number" class="w-20" min="0" :max="getMaxQuantity(item)" v-model="form.items[index].quantity_damaged" @input="updateQuantities(index, 'damaged')" />
                                            </td>
                                            <td>
                                                <TextInput type="number" class="w-20" min="0" :max="getMaxQuantity(item)" v-model="form.items[index].quantity_lost" @input="updateQuantities(index, 'lost')" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="p-6 border-t">
                             <InputLabel for="notes" value="Observações da Devolução" />
                             <textarea id="notes" v-model="form.notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="flex items-center justify-end p-6">
                            <PrimaryButton :disabled="form.processing">
                                Confirmar Devolução
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
