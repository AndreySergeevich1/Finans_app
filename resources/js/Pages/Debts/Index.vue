<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import DebtItem from '@/Components/DebtItem.vue'; // Используем из Dashboard
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  debts: Object, // Объект пагинации
  // filters: Object, // Для фильтров
});
</script>
<template>
    <Head title="Долги и Кредиты" />
    <AuthenticatedLayout>
        <template #header>
             <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Долги и Кредиты</h2>
                <Link :href="route('debts.create')">
                    <PrimaryButton>+ Добавить долг/кредит</PrimaryButton>
                </Link>
            </div>
             </template>
         <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                     <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="debts.data.length > 0" class="space-y-3">
                            <Link v-for="debt in debts.data" :key="debt.id" :href="route('debts.edit', debt.id)" class="block">
                                <DebtItem :debt="debt" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors duration-150"/>
                            </Link>
                        </div>
                        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
                            Нет долгов для отображения.
                            <Link :href="route('debts.create')" class="text-indigo-600 hover:underline font-medium ml-1">Добавить?</Link>
                        </div>
                        <Pagination class="mt-6" :links="debts.links" />
                    </div>
                 </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
