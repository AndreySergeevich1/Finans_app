<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecurringPaymentForm from '@/Components/RecurringPaymentForm.vue';

defineProps({
    accounts: Array,
    categories: Array,
    recurringPayment: Object
});
</script>

<template>
    <Head title="Редактирование обязательного платежа" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Редактирование обязательного платежа
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <RecurringPaymentForm
                            :accounts="accounts"
                            :categories="categories"
                            :recurring-payment="recurringPayment"
                            submit-route="recurring-payments.update"
                            cancel-route="recurring-payments.index"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 