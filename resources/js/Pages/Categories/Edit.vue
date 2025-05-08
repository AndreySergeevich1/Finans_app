<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoryForm from '@/Components/CategoryForm.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import { ref } from 'vue';

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
  potentialParents: {
    type: Array,
    default: () => [],
  },
});

// Deletion logic
const confirmingDeletion = ref(false);
const formDelete = useForm({});

const confirmDeletion = () => {
  confirmingDeletion.value = true;
  deleteCategory();
};

const deleteCategory = () => {
  formDelete.delete(route('categories.destroy', props.category.id), { preserveScroll: true });
};
</script>

<template>
  <Head :title="`Категория: ${category.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Редактировать категорию
        </h2>
        <DangerButton @click="confirmDeletion" :disabled="formDelete.processing">
          Удалить
        </DangerButton>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
          <CategoryForm
            :category="category"
            :potentialParents="potentialParents"
            :submitRoute="'categories.update'"
            :cancelRoute="'categories.index'"
            :submitButtonText="'Сохранить изменения'"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
