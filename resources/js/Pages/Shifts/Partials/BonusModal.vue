<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-80">
      <h4 class="text-lg font-semibold mb-4">Добавление премии</h4>
      <div class="flex flex-col gap-4">
        <div>
          <label class="block mb-1">Период с</label>
          <input type="date" v-model="modelStart" class="border rounded px-2 py-1 w-full" />
        </div>
        <div>
          <label class="block mb-1">Период по</label>
          <input type="date" v-model="modelEnd" class="border rounded px-2 py-1 w-full" />
        </div>
        <div>
          <label class="block mb-1">Ставка (за час)</label>
          <input type="number" v-model.number="modelRate" class="border rounded px-2 py-1 w-full" />
        </div>
      </div>
      <div class="mt-4 flex justify-end gap-2">
        <button @click="apply" class="px-4 py-2 bg-green-500 text-white rounded">Сохранить</button>
        <button @click="$emit('close')" class="px-4 py-2 text-gray-500">Отмена</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
const props = defineProps({
  start: String,
  end: String,
  rate: Number
})
const emit = defineEmits(['update:start', 'update:end', 'update:rate', 'apply', 'close'])

const modelStart = ref(props.start || '')
const modelEnd = ref(props.end || '')
const modelRate = ref(props.rate || 0)

watch(() => props.start, val => { modelStart.value = val })
watch(() => props.end, val => { modelEnd.value = val })
watch(() => props.rate, val => { modelRate.value = val })

function apply() {
  emit('update:start', modelStart.value)
  emit('update:end', modelEnd.value)
  emit('update:rate', modelRate.value)
  emit('apply')
}
</script>

<style scoped>
/* Можно добавить стили для модалки */
</style>
