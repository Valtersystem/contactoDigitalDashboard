<script setup lang="ts">
import { ref, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const showingNavigationDropdown = ref(false);
const page = usePage();

const isSidebarOpen = ref(false);

watch(() => page.props.flash, (flash: any) => {
    if (flash.success) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: flash.success,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }
    if (flash.error) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: flash.error,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }
}, { deep: true });
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <aside
            :class="['flex', 'h-full', 'flex-shrink-0', 'flex-col', 'justify-between', 'border-e', 'bg-white', 'w-64', 'transition-transform', 'duration-300', 'ease-in-out', 'lg:translate-x-0', { '-translate-x-full': !isSidebarOpen }]"
            class="absolute lg:relative z-20"
        >
            <div>
                <div class="px-4 py-6">
                    <Link :href="route('dashboard')">
                    <ApplicationLogo imageName="logo-2.png" class="block h-10 w-auto" />
                    </Link>
                </div>

                <nav class="mt-6 px-4">
                    <ul class="space-y-1">
                        <li>
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('clients.index')" :active="route().current('clients.*')">
                                Clientes
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('categories.index')" :active="route().current('categories.*')">
                                Categorias
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('products.index')" :active="route().current('products.*')">
                                Produtos
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('asset-management.index')"
                                :active="route().current('asset-management.*')">
                                Ativos Danificados/Perdidos
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('rentals.index')" :active="route().current('rentals.index')">
                                Histórico de Alugueis
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('reports.index')" :active="route().current('reports.index')">
                                Relatórios
                            </NavLink>
                        </li>
                        <li class="pt-4">
                            <Link :href="route('subscriptions.index')"
                                class="w-full flex items-center justify-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-400 transition-colors">
                            Assinaturas
                            </Link>
                        </li>
                        <li class="pt-4">
                            <Link :href="route('rentals.create')"
                                class="w-full flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-light transition-colors">
                            Novo Aluguel
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="sticky inset-x-0 bottom-0 border-t border-gray-100 p-2">
                <div class="hidden sm:ms-6 sm:flex sm:items-center">
                    <div class="relative w-full">
                        <Dropdown align="top" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md w-full">
                                    <button type="button"
                                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none w-full justify-between">
                                        {{ $page.props.auth.user.name }}
                                        <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex items-center">
                    <button @click="isSidebarOpen = !isSidebarOpen" class="lg:hidden mr-4 text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <slot name="header" />
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 bg-black opacity-50 z-10 lg:hidden"></div>
    </div>
</template>

<style scoped>
a.inline-flex {
    width: 100%;
    padding: 0.75rem 1rem;
    /* 12px 16px */
    border-left-width: 4px;
    border-bottom-width: 0;
}

a.border-indigo-400 {
    background-color: #f0f0f0;
}
</style>
