<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h4 class="text-lg font-semibold mb-4">Шаблоны смены на {{ date }}</h4>
      <div class="flex flex-col gap-2">
        <button
          v-for="tpl in templates"
          :key="tpl.type"
          @click="$emit('select', tpl.type)"
          class="px-4 py-2 bg-blue-500 text-white rounded"
        >
          {{ tpl.name }}
        </button>
      </div>
      <button @click="$emit('close')" class="mt-4 text-gray-500">Отмена</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  date: String,
  templates: Array
});
</script>

<style scoped>
/* любые дополнительные стили */
</style>
