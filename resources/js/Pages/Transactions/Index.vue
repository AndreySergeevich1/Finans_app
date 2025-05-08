<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionForm from '@/Components/TransactionForm.vue';

defineProps({
    accounts: Array,
    incomeCategories: Array,
    expenseCategories: Array,
    transactions: Array
});

const form = useForm({});

const deleteTransaction = (id) => {
    if (confirm('Вы уверены, что хотите удалить эту транзакцию?')) {
        form.delete(route('transactions.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                // Успешное удаление
            },
            onError: (errors) => {
                console.error('Error deleting transaction:', errors);
            }
        });
    }
};
</script>

<template>
    <Head title="Транзакции" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Транзакции
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Новая транзакция</h3>
                            <TransactionForm
                                :accounts="accounts"
                                :income-categories="incomeCategories"
                                :expense-categories="expenseCategories"
                                submit-route="transactions.store"
                                cancel-route="transactions.index"
                            />
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-medium mb-4">История транзакций</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Дата</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Тип</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Сумма</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Счет</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Категория</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Описание</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="transaction in transactions" :key="transaction.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ new Date(transaction.transaction_date).toLocaleDateString() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span :class="{
                                                    'text-red-600 dark:text-red-400': transaction.type === 'expense',
                                                    'text-green-600 dark:text-green-400': transaction.type === 'income',
                                                    'text-blue-600 dark:text-blue-400': transaction.type === 'transfer'
                                                }">
                                                    {{ transaction.type === 'expense' ? 'Расход' : transaction.type === 'income' ? 'Доход' : 'Перевод' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ new Intl.NumberFormat('ru-RU', { style: 'currency', currency: transaction.account.currency }).format(transaction.amount) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ transaction.account.name }}
                                                <span v-if="transaction.type === 'transfer' && transaction.related_account">
                                                    → {{ transaction.related_account.name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ transaction.category ? transaction.category.name : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ transaction.description || '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                <div class="flex space-x-2">
                                                    <Link :href="route('transactions.edit', transaction.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Редактировать
                                                    </Link>
                                                    <button 
                                                        @click="deleteTransaction(transaction.id)" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                        :disabled="form.processing"
                                                    >
                                                        Удалить
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
