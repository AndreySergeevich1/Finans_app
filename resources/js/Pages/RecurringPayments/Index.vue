<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecurringPaymentForm from '@/Components/RecurringPaymentForm.vue';

defineProps({
    accounts: Array,
    categories: Array,
    recurringPayments: Array
});

const form = useForm({});

const deleteRecurringPayment = (id) => {
    if (confirm('Вы уверены, что хотите удалить этот обязательный платеж?')) {
        form.delete(route('recurring-payments.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                // Успешное удаление
            },
            onError: (errors) => {
                console.error('Error deleting recurring payment:', errors);
            }
        });
    }
};
</script>

<template>
    <Head title="Обязательные платежи" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Обязательные платежи
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Новый обязательный платеж</h3>
                            <RecurringPaymentForm
                                :accounts="accounts"
                                :categories="categories"
                                submit-route="recurring-payments.store"
                                cancel-route="recurring-payments.index"
                            />
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-medium mb-4">Список обязательных платежей</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Счет</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Категория</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Сумма</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Периодичность</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Дата начала</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Дата окончания</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Статус</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="payment in recurringPayments" :key="payment.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ payment.account.name }} ({{ payment.account.currency }})
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ payment.category.name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ new Intl.NumberFormat('ru-RU', { style: 'currency', currency: payment.account.currency }).format(payment.amount) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ {
                                                    'daily': 'Ежедневно',
                                                    'weekly': 'Еженедельно',
                                                    'monthly': 'Ежемесячно',
                                                    'quarterly': 'Ежеквартально',
                                                    'yearly': 'Ежегодно'
                                                }[payment.frequency] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ new Date(payment.start_date).toLocaleDateString() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ payment.end_date ? new Date(payment.end_date).toLocaleDateString() : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span :class="{
                                                    'text-green-600 dark:text-green-400': payment.is_active,
                                                    'text-red-600 dark:text-red-400': !payment.is_active
                                                }">
                                                    {{ payment.is_active ? 'Активен' : 'Неактивен' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                <div class="flex space-x-2">
                                                    <Link :href="route('recurring-payments.edit', payment.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Редактировать
                                                    </Link>
                                                    <button 
                                                        @click="deleteRecurringPayment(payment.id)" 
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