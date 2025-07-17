<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Product } from '@/types';
import Dropdown from '@/Components/Dropdown.vue';

defineProps<{
    product: Product;
}>();

defineEmits(['delete', 'manageAssets']);

</script>

<template>
    <div class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="aspect-h-4 aspect-w-3 bg-gray-200 sm:aspect-none group-hover:opacity-75 h-48">
            <img v-if="product.image_url" :src="`/storage/${product.image_url}`" :alt="product.name"
                class="h-full w-full object-cover object-center sm:h-full sm:w-full" />
            <div v-else class="h-full w-full flex items-center justify-center bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="flex flex-1 flex-col space-y-2 p-4">
            <h3 class="text-sm font-medium text-gray-900">
                <Link :href="route('products.edit', product.id)">
                    <span aria-hidden="true" class="absolute inset-0" />
                    {{ product.name }}
                </Link>
            </h3>
            <p class="text-sm text-gray-500">{{ product.category?.name }}</p>
            <div class="flex flex-1 flex-col justify-end">
                <div class="flex justify-between items-center">
                    <p class="text-sm italic text-gray-500">
                        <template v-if="product.tracking_type === 'SERIALIZED'">
                           {{ product.assets_count }} Ativos
                        </template>
                        <template v-else>
                           {{ product.stock_quantity }} em Estoque
                        </template>
                    </p>
                     <span
                        :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ product.is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
            </div>
        </div>
         <div class="absolute top-2 right-2">
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button class="p-2 rounded-full bg-white/60 hover:bg-white/90 backdrop-blur-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                    </button>
                </template>
                <template #content>
                    <Link :href="route('products.edit', product.id)" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100">Editar</Link>
                     <Link v-if="product.tracking_type === 'SERIALIZED'" :href="route('products.assets.index', product.id)" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100">Gerir Ativos</Link>
                    <button @click="$emit('delete', product.id)" class="block w-full px-4 py-2 text-left text-sm leading-5 text-red-600 hover:bg-gray-100">Apagar</button>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
