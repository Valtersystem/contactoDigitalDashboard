<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { Subscription, Client, Product } from '@/types';

interface Forecast {
    month: string;
    total_revenue: number;
}

defineProps<{
    monthlyForecast: Forecast[];
    overdueSubscriptions: (Subscription & { client: Client, product: Product })[];
    upcomingRenewals: (Subscription & { client: Client, product: Product })[];
}>();

const formatMonth = (monthString: string) => {
    const [year, month] = monthString.split('-');
    return new Date(parseInt(year), parseInt(month) - 1).toLocaleString('default', { month: 'long', year: 'numeric' });
};
</script>

<template>
    <Head title="Relatórios Financeiros" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relatórios Financeiros e Alertas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Previsão de Receita (Próximos Meses)</h3>
                    <div class="space-y-4">
                         <div v-if="monthlyForecast.length === 0" class="text-center text-gray-500 py-4">
                            Nenhuma previsão de receita disponível.
                        </div>
                        <div v-for="forecast in monthlyForecast" :key="forecast.month" class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <p class="font-semibold text-gray-800 capitalize">{{ formatMonth(forecast.month) }}</p>
                            <p class="text-xl font-bold text-green-600">€ {{ forecast.total_revenue.toFixed(2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border-l-4 border-red-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-red-800 mb-4">🚨 Alerta: Licenças Vencidas / Por Pagar</h3>
                    <div v-if="overdueSubscriptions.length === 0" class="text-center text-red-700 py-4">
                        Nenhuma licença vencida.
                    </div>
                    <ul v-else class="space-y-2">
                        <li v-for="sub in overdueSubscriptions" :key="sub.id" class="text-sm">
                            <span class="font-bold">{{ sub.client.business_name }}</span> - {{ sub.product.name }} (Venceu em: {{ new Date(sub.next_billing_date).toLocaleDateString() }})
                        </li>
                    </ul>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Próximas Renovações (30 dias)</h3>
                     <div v-if="upcomingRenewals.length === 0" class="text-center text-gray-500 py-4">
                        Nenhuma renovação agendada para os próximos 30 dias.
                    </div>
                    <ul v-else class="space-y-2">
                        <li v-for="sub in upcomingRenewals" :key="sub.id" class="text-sm flex justify-between">
                            <span><span class="font-bold">{{ sub.client.business_name }}</span> - {{ sub.product.name }}</span>
                            <span class="text-gray-600">Vence em: {{ new Date(sub.next_billing_date).toLocaleDateString() }}</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
