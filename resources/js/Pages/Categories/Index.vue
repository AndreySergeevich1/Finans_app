<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import CategoryItem from '@/Components/CategoryItem.vue'; // Новый компонент
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  categories: Object, // Объект пагинации
});
</script>
<template>
    <Head title="Категории" />
    <AuthenticatedLayout>
        <template #header>
             <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Категории</h2>
                <Link :href="route('categories.create')">
                    <PrimaryButton>+ Добавить категорию</PrimaryButton>
                </Link>
            </div>
        </template>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="categories.data.length > 0" class="space-y-1">
                             <Link v-for="cat in categories.data" :key="cat.id" :href="route('categories.edit', cat.id)" class="block">
                                <CategoryItem :category="cat" class="cursor-pointer"/>
                             </Link>
                        </div>
                         <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
                            Нет категорий для отображения.
                            <Link :href="route('categories.create')" class="text-indigo-600 hover:underline font-medium ml-1">Добавить?</Link>
                        </div>
                        <Pagination class="mt-6" :links="categories.links" />
                    </div>
                 </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
