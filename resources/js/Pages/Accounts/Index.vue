<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AccountCard from '@/Components/AccountCard.vue'; // Используем созданную ранее карточку
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue'; // Нужен компонент пагинации

defineProps({
  accounts: Object, // Объект пагинации
});
</script>

<template>
    <Head title="Мои Счета" />
    <AuthenticatedLayout>
        <template #header>
             <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Мои Счета</h2>
                <Link :href="route('accounts.create')">
                    <PrimaryButton>+ Добавить счет</PrimaryButton>
                </Link>
            </div>
        </template>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="accounts.data.length > 0" class="space-y-4">
                     <Link v-for="account in accounts.data" :key="account.id" :href="route('accounts.edit', account.id)">
                        <AccountCard :account="account" class="cursor-pointer" />
                     </Link>
                </div>
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                    У вас пока нет счетов.
                    <Link :href="route('accounts.create')" class="text-indigo-600 hover:underline font-medium ml-1">Добавить?</Link>
                </div>
                <Pagination class="mt-6" :links="accounts.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
