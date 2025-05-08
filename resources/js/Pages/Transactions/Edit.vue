<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionForm from '@/Components/TransactionForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue'; // Для кнопки удаления
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Получаем данные из TransactionController@edit
const props = defineProps({
    transaction: Object, // Редактируемая транзакция
    accounts: Array,
    incomeCategories: Array,
    expenseCategories: Array,
});

// Логика для подтверждения удаления
const confirmingDeletion = ref(false);
const formDelete = useForm({}); // Пустая форма для DELETE запроса

const confirmDeletion = () => {
    confirmingDeletion.value = true;
    // TODO: Показать модальное окно подтверждения
    // В этом примере просто вызовем deleteTransaction сразу
     deleteTransaction();
};

const deleteTransaction = () => {
     formDelete.delete(route('transactions.destroy', props.transaction.id), {
        preserveScroll: true,
        onSuccess: () => { /* Редирект произойдет автоматически */ },
        onError: () => { confirmingDeletion.value = false; /* Показать ошибку */ },
     });
};

</script>

<template>
    <Head :title="'Редактировать транзакцию #' + transaction.id" />

    <AuthenticatedLayout>
        <template #header>
             <div class="flex justify-between items-center">
                 <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Редактировать транзакцию</h2>
                 <DangerButton @click="confirmDeletion" :disabled="formDelete.processing">
                        Удалить
                  </DangerButton>
             </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 md:p-8">
                        <TransactionForm
                            :transaction="transaction" :accounts="accounts"
                            :income-categories="incomeCategories"
                            :expense-categories="expenseCategories"
                            :submitRoute="'transactions.update'"
                            :cancelRoute="'transactions.index'"
                            :submitButtonText="'Сохранить изменения'"
                        />
                    </div>
                </div>
            </div>
        </div>
        </AuthenticatedLayout>
</template>
