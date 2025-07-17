<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = withDefaults(
    defineProps<{
        title: string;
        value: number | string;
        link: string;
        variant?: 'default' | 'warning' | 'danger';
    }>(),
    {
        variant: 'default',
    },
);

const valueClasses = computed(() => {
    switch (props.variant) {
        case 'warning':
            return 'text-yellow-600';
        case 'danger':
            return 'text-red-600';
        default:
            return 'text-gray-900';
    }
});
</script>

<template>
    <Link :href="link" class="block p-6 transition bg-white shadow-sm hover:bg-gray-50 sm:rounded-lg border">
        <h3 class="text-sm font-medium text-gray-500 truncate">{{ title }}</h3>
        <p class="mt-1 text-3xl font-semibold" :class="valueClasses">{{ value }}</p>
    </Link>
</template>
