<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AccountForm from '@/Components/AccountForm.vue'; // Используем форму счета
import { Head } from '@inertiajs/vue3';
</script>
<template>
    <Head title="Новый счет" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Новый счет</h2>
        </template>
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                    <AccountForm
                        :submitRoute="'accounts.store'"
                        :cancelRoute="'accounts.index'"
                        :submitButtonText="'Создать счет'"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
