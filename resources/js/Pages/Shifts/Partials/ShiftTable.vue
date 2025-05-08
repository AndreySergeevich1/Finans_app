<template>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded shadow">
        <thead>
          <tr>
            <th class="px-4 py-2 border-b">Дата</th>
            <th class="px-4 py-2 border-b">Начало</th>
            <th class="px-4 py-2 border-b">Конец</th>
            <th class="px-4 py-2 border-b">Тип</th>
            <th class="px-4 py-2 border-b">Примечания</th>
            <th class="px-4 py-2 border-b">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="shift in shifts" :key="shift.id" class="hover:bg-gray-50">
            <td class="px-4 py-2">{{ formatDate(shift.date) }}</td>
            <td class="px-4 py-2">{{ formatTime(shift.start_time) }}</td>
            <td class="px-4 py-2">{{ formatTime(shift.end_time) }}</td>
            <td class="px-4 py-2">
              <span
                class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold"
                :class="{
                  'bg-green-100 text-green-800': shift.type === 'regular',
                  'bg-yellow-100 text-yellow-800': shift.type === 'overtime',
                  'bg-blue-100 text-blue-800': shift.type === 'night',
                }"
              >
                <span v-if="shift.type === 'regular'">🟢 Обычная</span>
                <span v-else-if="shift.type === 'overtime'">🟡 Сверхурочная</span>
                <span v-else>🔵 Ночная</span>
              </span>
            </td>
            <td class="px-4 py-2">{{ shift.notes }}</td>
            <td class="px-4 py-2">
              <button @click="$emit('edit', shift)" class="text-indigo-600 hover:text-indigo-900 mr-3 underline">Редактировать</button>
              <button @click="$emit('delete', shift)" class="text-red-600 hover:text-red-900 underline">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  <script setup>
  defineProps({
    shifts: Array,
    formatDate: Function,
    formatTime: Function
  })
  </script>