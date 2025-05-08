<template>
  <Head title="Обязательные платежи" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Обязательные платежи
        </h2>
        <Link
          :href="route('mandatory-payments.create')"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
        >
          Добавить платеж
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div v-if="payments.data.length === 0" class="text-center text-gray-500 dark:text-gray-400">
              Нет обязательных платежей
            </div>
            <div v-else class="space-y-4">
              <MandatoryPaymentItem
                v-for="payment in payments.data"
                :key="payment.id"
                :payment="payment"
                class="cursor-pointer"
                @click="editPayment(payment)"
              />
              <MandatoryPaymentItem
                v-for="payment in debtPayments"
                :key="'debt-' + payment.id"
                :payment="payment"
                class="cursor-pointer"
                @click="editDebt(payment)"
              />
            </div>

            <Pagination
              v-if="payments.data.length > 0"
              :links="payments.links"
              class="mt-6"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MandatoryPaymentItem from '@/Components/MandatoryPaymentItem.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  payments: {
    type: Object,
    required: true
  },
  debtPayments: {
    type: Array,
    required: true
  }
});

const editPayment = (payment) => {
  router.visit(route('mandatory-payments.edit', payment.id));
};

const editDebt = (payment) => {
  router.visit(route('debts.edit', payment.id));
};
</script> 