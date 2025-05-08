<script setup>
import { computed } from 'vue';
import { TagIcon, ArrowUpCircleIcon, ArrowDownCircleIcon } from '@heroicons/vue/24/outline'; // Пример иконок

const props = defineProps({
    category: { type: Object, required: true },
});

const typeIcon = computed(() => props.category.type === 'income' ? ArrowUpCircleIcon : ArrowDownCircleIcon);
const typeColor = computed(() => props.category.type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400');

</script>
<template>
    <div class="flex items-center justify-between py-3 px-1 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-md transition-colors duration-150">
        <div class="flex items-center overflow-hidden mr-2">
             <span class="mr-3 flex-shrink-0 p-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                 <TagIcon class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </span>
             <div class="overflow-hidden">
                 <div class="font-medium text-gray-800 dark:text-gray-200 truncate text-sm sm:text-base">
                    {{ category.name }}
                 </div>
                 <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">
                    <span v-if="category.parent_category"> {{ category.parent_category.name }} / </span>
                    {{ category.type === 'income' ? 'Доход' : 'Расход' }}
                </div>
             </div>
        </div>
        <div class="text-right text-sm sm:text-base flex-shrink-0 pl-2" :class="typeColor">
            <component :is="typeIcon" class="w-5 h-5 inline-block" />
        </div>
    </div>
</template>
