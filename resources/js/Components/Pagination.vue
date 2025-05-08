<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array, // Массив ссылок от Laravel Paginator
});
</script>

<template>
    <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1 justify-center"> <template v-for="(link, key) in links" :key="key">
        <div
          v-if="link.url === null"
          class="mr-1 mb-1 px-3 py-2 text-sm leading-4 text-gray-400 dark:text-gray-500 border rounded"
          v-html="link.label"
        />
        <Link
          v-else
          class="mr-1 mb-1 px-3 py-2 text-sm leading-4 border rounded focus:border-indigo-500 focus:text-indigo-500 dark:focus:border-indigo-600 dark:focus:text-indigo-400"
          :class="{ 'bg-indigo-600 text-white dark:bg-indigo-400 dark:text-gray-900 border-indigo-600 dark:border-indigo-400': link.active, 'hover:bg-gray-100 dark:hover:bg-gray-700': !link.active }"
          :href="link.url"
          v-html="link.label"
          preserve-scroll
          preserve-state
        />
         </template>
    </div>
  </div>
</template>
