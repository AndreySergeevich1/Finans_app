<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionForm from '@/Components/TransactionForm.vue';
import { Head } from '@inertiajs/vue3';

// Получаем списки из TransactionController@create
defineProps({
    accounts: Array,
    incomeCategories: Array,
    expenseCategories: Array,
    prefilledType: String,
    modelValue: String,
});
</script>

<template>
    <Head title="Новая транзакция" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Новая транзакция</h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 md:p-8">
                        <TransactionForm
                            :accounts="accounts"
                            :income-categories="incomeCategories"
                            :expense-categories="expenseCategories"
                            :prefilled-type="prefilledType"
                            :submitRoute="'transactions.store'"
                            :cancelRoute="'dashboard'"
                            :submitButtonText="'Добавить транзакцию'"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
