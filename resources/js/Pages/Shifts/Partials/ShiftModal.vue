<template>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded shadow-lg w-96">
        <h3 class="text-lg font-medium mb-4">{{ shift ? 'Редактировать смену' : 'Добавить смену' }}</h3>
        <form @submit.prevent="save">
          <div class="mb-4">
            <label class="block mb-1">Дата</label>
            <input type="date" v-model="form.date" class="w-full border rounded px-2 py-1" required>
          </div>
          <div class="mb-4">
            <label class="block mb-1">Время начала</label>
            <input type="time" v-model="form.start_time" class="w-full border rounded px-2 py-1" required>
          </div>
          <div class="mb-4">
            <label class="block mb-1">Время окончания</label>
            <input type="time" v-model="form.end_time" class="w-full border rounded px-2 py-1" required>
          </div>
          <div class="mb-4">
            <label class="block mb-1">Тип смены</label>
            <select v-model="form.type" class="w-full border rounded px-2 py-1" required>
              <option value="regular">Обычная</option>
              <option value="overtime">Сверхурочная</option>
              <option value="night">Ночная</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block mb-1">Примечания</label>
            <textarea v-model="form.notes" class="w-full border rounded px-2 py-1"></textarea>
          </div>
          <div class="flex justify-end">
            <button type="button" @click="$emit('close')" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Отмена</button>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Сохранить</button>
          </div>
          <div v-if="error" class="text-red-600 mt-2">{{ error }}</div>
        </form>
      </div>
    </div>
  </template>
  <script setup>
  import { ref, watch, reactive } from 'vue'
  import { router } from '@inertiajs/vue3'
  
  const props = defineProps({ shift: Object })
  const emit = defineEmits(['close', 'saved'])
  
  const form = reactive({
    date: '',
    start_time: '',
    end_time: '',
    type: 'regular',
    notes: ''
  })
  const error = ref('')
  
  watch(() => props.shift, (val) => {
    if (val) {
      Object.assign(form, val)
    } else {
      form.date = ''
      form.start_time = ''
      form.end_time = ''
      form.type = 'regular'
      form.notes = ''
    }
  }, { immediate: true })
  
  function save() {
    error.value = ''
    const url = props.shift ? `/shifts/${props.shift.id}` : '/shifts'
    const method = props.shift ? 'put' : 'post'
    router[method](url, form, {
      onSuccess: () => emit('saved'),
      onError: (errs) => {
        error.value = Object.values(errs).flat().join(' ')
      }
    })
  }
  </script>