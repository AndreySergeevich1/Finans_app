
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DebtForm from '@/Components/DebtForm.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    debt: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        default: () => []
    }
});

// Форма для добавления платежа
const paymentForm = useForm({
    account_id: props.accounts[0]?.id || null,
    amount: '',
    payment_date: new Date().toISOString().slice(0, 10),
    notes: '',
});

const submitPayment = () => {
    paymentForm.post(route('debts.payments.store', props.debt.id), {
        preserveScroll: true,
        onSuccess: () => paymentForm.reset(), // Сбрасываем форму после успеха
        onError: (errors) => { console.error("Payment Error:", errors); }
    });
};



const confirmingDeletion = ref(false);
const formDelete = useForm({});

const confirmDeletion = () => {
  confirmingDeletion.value = true;
  deleteDebt();
};

const deleteDebt = () => {
  formDelete.delete(route('debts.destroy', props.debt.id), { preserveScroll: true });
};

function formatCurrency(amount) {
  if (!amount) return '0 ₽';
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0
  }).format(amount);
}
</script>

<template>
    <Head :title="'Долг: ' + debt.lender_or_borrower" />
    <AuthenticatedLayout>
        <template #header>
             </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Детали долга/кредита</h3>
                    <DebtForm
                        :debt="debt"
                        :submitRoute="'debts.update'"
                        :cancelRoute="'debts.index'"
                        :submitButtonText="'Сохранить изменения'"
                    />
                </div>

                <div v-if="debt.status === 'active' || debt.status === 'overdue'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Добавить платеж</h3>
                     <form @submit.prevent="submitPayment">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div>
                                <InputLabel for="payment_account_id" value="Счет списания" />
                                <select id="payment_account_id" v-model="paymentForm.account_id" required class="mt-1 block w-full form-select">
                                    <option v-if="!accounts.length" disabled>Нет доступных счетов</option>
                                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                                        {{ account.name }} ({{ formatCurrency(account.balance, account.currency) }}) </option>
                                </select>
                                <InputError class="mt-2" :message="paymentForm.errors.account_id" />
                            </div>

                            <div>
                                <InputLabel for="payment_amount" value="Сумма платежа" />
                                <TextInput id="payment_amount" type="number" step="0.01" min="0.01" :max="debt.remaining_amount" class="mt-1 block w-full" v-model="paymentForm.amount" required placeholder="0.00" />
                                <InputError class="mt-2" :message="paymentForm.errors.amount" />
                                <p class="text-xs text-gray-500 mt-1">Макс: {{ formatCurrency(debt.remaining_amount, debt.currency) }}</p>
                            </div>

                            <div>
                                <InputLabel for="payment_date" value="Дата платежа" />
                                <TextInput id="payment_date" type="date" class="mt-1 block w-full" v-model="paymentForm.payment_date" required />
                                <InputError class="mt-2" :message="paymentForm.errors.payment_date" />
                            </div>

                            <div>
                                <InputLabel for="payment_notes" value="Заметка (необязательно)" />
                                <TextInput id="payment_notes" type="text" class="mt-1 block w-full" v-model="paymentForm.notes" placeholder="Напр., Ежемесячный взнос" />
                                <InputError class="mt-2" :message="paymentForm.errors.notes" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <PrimaryButton :class="{ 'opacity-25': paymentForm.processing }" :disabled="paymentForm.processing">
                                Зафиксировать платеж
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                 </div>
        </div>
        </AuthenticatedLayout>
</template>
