<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoryForm from '@/Components/CategoryForm.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  potentialParents: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <Head title="Новая категория" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Новая категория
      </h2>
    </template>

    <div class="py-8">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
          <CategoryForm
            :potentialParents="potentialParents"
            :submitRoute="'categories.store'"
            :cancelRoute="'categories.index'"
            :submitButtonText="'Создать категорию'"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
