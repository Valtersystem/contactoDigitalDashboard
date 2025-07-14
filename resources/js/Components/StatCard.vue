<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title: string;
    value: number | string;
    link: string;
}>();
</script>

<template>
    <Link :href="link" class="block p-6 transition bg-white shadow-sm hover:bg-gray-50 sm:rounded-lg">
        <h3 class="text-sm font-medium text-gray-500 truncate">{{ title }}</h3>
        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ value }}</p>
    </Link>
</template>
