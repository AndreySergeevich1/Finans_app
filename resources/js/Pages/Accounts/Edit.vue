<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AccountForm from '@/Components/AccountForm.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import { ref } from 'vue';

const props = defineProps({
    account: Object, // Данные счета из AccountController@edit
});

// Логика удаления
const confirmingDeletion = ref(false);
const formDelete = useForm({});
const confirmDeletion = () => { confirmingDeletion.value = true; deleteAccount(); }; // Упрощено без модалки
const deleteAccount = () => {
     formDelete.delete(route('accounts.destroy', props.account.id), { preserveScroll: true });
};
</script>
<template>
    <Head :title="'Счет: ' + account.name" />
    <AuthenticatedLayout>
        <template #header>
             <div class="flex justify-between items-center">
                 <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Редактировать счет</h2>
                  <DangerButton @click="confirmDeletion" :disabled="formDelete.processing">Удалить</DangerButton>
             </div>
        </template>
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                     <AccountForm
                        :account="account"
                        :submitRoute="'accounts.update'"
                        :cancelRoute="'accounts.index'"
                        :submitButtonText="'Сохранить изменения'"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
