<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow sm:rounded-lg p-6">
        <!-- Заголовок и навигация -->
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">График смен за {{ formatMonth(activeYear, activeMonth) }}</h3>
          <div class="space-x-2">
            <button @click="changePeriod(-1)" class="px-3 py-1 bg-gray-200 rounded">←</button>
            <button @click="changePeriod(1)" class="px-3 py-1 bg-gray-200 rounded">→</button>
          </div>
        </div>

        <!-- Ставка зарплаты -->
        <div class="mb-6">
          <label class="block mb-1">Ставка (₽/час)</label>
          <input type="number" v-model.number="salaryRate" class="border rounded px-2 py-1 w-32" />
        </div>

        <!-- Календарные сегменты -->
        <div class="mb-8">
          <div v-if="isFirstPeriod">
            <h4 class="font-medium mb-2">30–14</h4>
            <div class="grid grid-cols-7 gap-2 mb-4">
              <div
                v-for="day in daysSegment1"
                :key="day.date"
                class="border rounded p-2 cursor-pointer hover:bg-gray-100 relative"
              >
                <div class="font-semibold">{{ formatDay(day.date) }}</div>
                <div v-for="shift in day.shifts" :key="shift.id" class="text-sm">
                  {{ getShiftName(shift.type) }}
                </div>
                <!-- Кнопки для быстрого добавления смены по шаблону -->
                <div class="absolute bottom-1 left-1 flex gap-1">
                  <button @click="addShiftByTemplate(day.date, 'regular')" class="text-xs bg-blue-200 hover:bg-blue-400 text-blue-900 rounded px-1">О</button>
                  <button @click="addShiftByTemplate(day.date, 'day')" class="text-xs bg-yellow-200 hover:bg-yellow-400 text-yellow-900 rounded px-1">Д</button>
                  <button @click="addShiftByTemplate(day.date, 'evening')" class="text-xs bg-purple-200 hover:bg-purple-400 text-purple-900 rounded px-1">В</button>
                </div>
              </div>
            </div>
            <table class="w-full table-auto bg-white">
              <thead>
                <tr class="bg-gray-50">
                  <th class="px-2 py-1">Дата</th>
                  <th class="px-2 py-1">Начало</th>
                  <th class="px-2 py-1">Конец</th>
                  <th class="px-2 py-1">Зарплата</th>
                  <th class="px-2 py-1">Премия</th>
                  <th class="px-2 py-1">Примечания</th>
                  <th class="px-2 py-1">Действия</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in period1Shifts" :key="s.id" class="border-t">
                  <td class="px-2 py-1">{{ formatDate(s.date) }}</td>
                  <td class="px-2 py-1">{{ s.start_time }}</td>
                  <td class="px-2 py-1">{{ s.end_time }}</td>
                  <td class="px-2 py-1">{{ (shiftHours(s) * salaryRate).toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ shiftBonus(s).toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ s.notes ? s.notes : '' }}</td>
                  <td class="px-2 py-1">
                    <button @click="openEditModal(s)" class="text-blue-500 mr-2">Ред.</button>
                    <button @click="deleteShift(s)" class="text-red-500">Удал.</button>
                  </td>
                </tr>
                <tr class="font-semibold bg-gray-100">
                  <td colspan="3" class="px-2 py-1">Итого</td>
                  <td class="px-2 py-1">{{ totalSalary1.toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ totalBonus1.toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ (totalSalary1 + totalBonus1).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else>
            <h4 class="font-medium mb-2">15–29</h4>
            <div class="grid grid-cols-7 gap-2 mb-4">
              <div
                v-for="day in daysSegment2"
                :key="day.date"
                class="border rounded p-2 cursor-pointer hover:bg-gray-100 relative"
              >
                <div class="font-semibold">{{ formatDay(day.date) }}</div>
                <div v-for="shift in day.shifts" :key="shift.id" class="text-sm">
                  {{ getShiftName(shift.type) }}
                </div>
                <!-- Кнопки для быстрого добавления смены по шаблону -->
                <div class="absolute bottom-1 left-1 flex gap-1">
                  <button @click="addShiftByTemplate(day.date, 'regular')" class="text-xs bg-blue-200 hover:bg-blue-400 text-blue-900 rounded px-1">О</button>
                  <button @click="addShiftByTemplate(day.date, 'day')" class="text-xs bg-yellow-200 hover:bg-yellow-400 text-yellow-900 rounded px-1">Д</button>
                  <button @click="addShiftByTemplate(day.date, 'evening')" class="text-xs bg-purple-200 hover:bg-purple-400 text-purple-900 rounded px-1">В</button>
                </div>
              </div>
            </div>
            <table class="w-full table-auto bg-white">
              <thead>
                <tr class="bg-gray-50">
                  <th class="px-2 py-1">Дата</th>
                  <th class="px-2 py-1">Начало</th>
                  <th class="px-2 py-1">Конец</th>
                  <th class="px-2 py-1">Зарплата</th>
                  <th class="px-2 py-1">Премия</th>
                  <th class="px-2 py-1">Примечания</th>
                  <th class="px-2 py-1">Действия</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in period2Shifts" :key="s.id" class="border-t">
                  <td class="px-2 py-1">{{ formatDate(s.date) }}</td>
                  <td class="px-2 py-1">{{ s.start_time }}</td>
                  <td class="px-2 py-1">{{ s.end_time }}</td>
                  <td class="px-2 py-1">{{ (shiftHours(s) * salaryRate).toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ shiftBonus(s).toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ s.notes ? s.notes : '' }}</td>
                  <td class="px-2 py-1">
                    <button @click="openEditModal(s)" class="text-blue-500 mr-2">Ред.</button>
                    <button @click="deleteShift(s)" class="text-red-500">Удал.</button>
                  </td>
                </tr>
                <tr class="font-semibold bg-gray-100">
                  <td colspan="3" class="px-2 py-1">Итого</td>
                  <td class="px-2 py-1">{{ totalSalary2.toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ totalBonus2.toFixed(2) }}</td>
                  <td class="px-2 py-1">{{ (totalSalary2 + totalBonus2).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Кнопки модалок -->
        <div class="space-x-2">
          <button @click="openBonusModal" class="bg-green-500 text-white px-4 py-2 rounded">Добавить премию</button>
        </div>

        <!-- Модалка: выбор шаблона -->
        <ShiftTemplateModal
          v-if="templateModalOpen"
          :date="selectedDate"
          :templates="shiftTemplates"
          @select="setTemplate"
          @close="templateModalOpen = false"
        />

        <!-- Модалка: премия -->
        <BonusModal
          v-if="bonusModalOpen"
          :firstPeriodShifts="props.firstPeriodShifts || []"
          :secondPeriodShifts="props.secondPeriodShifts || []"
          v-model:start="bonusStart"
          v-model:end="bonusEnd"
          v-model:rate="bonusRate"
          @apply="applyBonus"
          @close="bonusModalOpen = false"
        />

        <!-- Модалка для редактирования смены -->
        <ShiftModal
          v-if="modalOpen"
          :shift="modalShift"
          @close="closeModal"
          @saved="onShiftSaved"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ShiftTemplateModal from './Partials/ShiftTemplateModal.vue'
import BonusModal from './Partials/BonusModal.vue'
import ShiftModal from './Partials/ShiftModal.vue'

const props = defineProps({ firstPeriodShifts: Array, secondPeriodShifts: Array })

// Текущая дата и смещение месяцев
const today = new Date()
const periodOffset = ref(0) // 0 — текущий период, +1 — следующий, -1 — предыдущий
const isFirstPeriod = ref(true) // true: 30–14, false: 15–29

const activeDate = computed(() => {
  const d = new Date(today)
  d.setMonth(d.getMonth() + periodOffset.value)
  return d
})
const activeYear = computed(() => activeDate.value.getFullYear())
const activeMonth = computed(() => activeDate.value.getMonth())

function changePeriod(delta) {
  if (isFirstPeriod.value) {
    if (delta > 0) {
      isFirstPeriod.value = false
    } else {
      periodOffset.value--
      isFirstPeriod.value = false
    }
  } else {
    if (delta > 0) {
      periodOffset.value++
      isFirstPeriod.value = true
    } else {
      isFirstPeriod.value = true
    }
  }
  // После смены периода перезагружаем данные с параметрами периода
  reloadWithPeriod()
}

function reloadWithPeriod() {
  router.reload({
    data: {
      year: activeYear.value,
      month: activeMonth.value + 1, // JS: 0-11, PHP: 1-12
      isFirstPeriod: isFirstPeriod.value
    },
    only: ['firstPeriodShifts', 'secondPeriodShifts']
  })
}

function formatMonth(year, month) {
  return `${month + 1}.${year}`
}

// Ставка заработной платы за час
const salaryRate = ref(0)

// Шаблоны смен (исправлено по требованиям)
const shiftTemplates = [
  { type: 'regular', name: 'Обычная смена', start_time: '10:00', end_time: '00:00' },
  { type: 'day', name: 'Дневная смена', start_time: '13:00', end_time: '21:00' },
  { type: 'evening', name: 'Вечерняя смена', start_time: '17:00', end_time: '00:00' }
]

// Все смены
const allShifts = computed(() => [...props.firstPeriodShifts, ...props.secondPeriodShifts])

// Группировка по дате
function groupByDate(shifts) {
  return shifts.reduce((map, s) => {
    const key = s.date.split('T')[0]
    if (!map[key]) map[key] = []
    map[key].push(s)
    return map
  }, {})
}
const shiftsByDate = computed(() => groupByDate(allShifts.value))

// Генерация дней
function genDays(start, end) {
  const days = []
  let d = new Date(start.getTime())
  while (d <= end) {
    const date = new Date(d.getTime())
    // Формируем ключ в формате YYYY-MM-DD в локальном времени
    const key = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0')
    days.push({ date, shifts: shiftsByDate.value[key] || [] })
    d.setDate(d.getDate() + 1)
  }
  return days
}

const daysSegment1 = computed(() => {
  // 30–14
  const y = activeYear.value
  const m = activeMonth.value
  const prevMonth = m === 0 ? 11 : m - 1
  const prevYear = m === 0 ? y - 1 : y
  let startDay = 30
  const lastDayPrevMonth = new Date(prevYear, prevMonth + 1, 0).getDate()
  if (startDay > lastDayPrevMonth) startDay = lastDayPrevMonth
  const startDate = new Date(prevYear, prevMonth, startDay)
  return genDays(startDate, new Date(y, m, 14))
})

const daysSegment2 = computed(() =>
  genDays(new Date(activeYear.value, activeMonth.value, 15), new Date(activeYear.value, activeMonth.value, 29))
)

// Выбор шаблона
const templateModalOpen = ref(false)
const selectedDate = ref(null)
function openTemplateModal(date) {
  selectedDate.value = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0')
  templateModalOpen.value = true
}
function setTemplate(type) {
  const tpl = shiftTemplates.find(t => t.type === type)
  if (!tpl) return
  router.post('/shifts', {
    date: selectedDate.value,
    type: tpl.type,
    start_time: tpl.start_time,
    end_time: tpl.end_time,
    notes: '',
    year: activeYear.value,
    month: activeMonth.value + 1,
    isFirstPeriod: isFirstPeriod.value
  }, {
    onSuccess: () => {
      templateModalOpen.value = false
      reloadWithPeriod()
    }
  })
}

// Премии
const bonusModalOpen = ref(false)
const bonusStart = ref(null)
const bonusEnd = ref(null)
const bonusRate = ref(0)
const bonuses = ref([])

function applyBonus() {
  // считаем часы только по отображаемому периоду
  const periodShifts = isFirstPeriod.value ? period1Shifts.value : period2Shifts.value
  const start = new Date(bonusStart.value)
  const end = new Date(bonusEnd.value)
  if (end < start) return // не даём добавить премию с некорректным диапазоном

  const hours = periodShifts
    .filter(s => {
      const d = new Date(s.date)
      return d >= start && d <= end
    })
    .reduce((sum, s) => sum + shiftHours(s), 0)

  // Премия только для текущего отображаемого периода
  bonuses.value = bonuses.value.filter(b => {
    // Удаляем премии, которые пересекаются с этим же периодом
    return !(b.start === bonusStart.value && b.end === bonusEnd.value && b.rate === bonusRate.value && b.periodKey === periodKey())
  })
  bonuses.value.push({ start: bonusStart.value, end: bonusEnd.value, amount: hours * bonusRate.value, rate: bonusRate.value, periodKey: periodKey() })
  bonusModalOpen.value = false
}

function periodKey() {
  // Уникальный ключ для текущего отображаемого периода
  return `${activeYear.value}-${activeMonth.value + 1}-${isFirstPeriod.value ? 'first' : 'second'}`
}

function shiftBonus(s) {
  return bonuses.value.reduce((sum, b) => {
    // Премия только для текущего отображаемого периода
    if (b.periodKey !== periodKey()) return sum
    const d = new Date(s.date)
    const start = new Date(b.start)
    const end = new Date(b.end)
    if (end < start) return sum // пропускаем некорректные премии
    if (d >= start && d <= end) {
      // Премия за смену = ставка * часы смены
      return sum + b.rate * shiftHours(s)
    }
    return sum
  }, 0)
}

const period1Shifts = computed(() => {
  const y = activeYear.value
  const m = activeMonth.value
  // Определяем предыдущий месяц и год
  const prevMonth = m === 0 ? 11 : m - 1
  const prevYear = m === 0 ? y - 1 : y
  const start = new Date(prevYear, prevMonth, 30)
  const end = new Date(y, m, 14, 23, 59, 59)
  return allShifts.value.filter(s => {
    const d = new Date(s.date)
    return d >= start && d <= end
  })
})

const period2Shifts = computed(() => {
  const y = activeYear.value
  const m = activeMonth.value
  const start = new Date(y, m, 15)
  const end = new Date(y, m, 29, 23, 59, 59)
  return allShifts.value.filter(s => {
    const d = new Date(s.date)
    return d >= start && d <= end
  })
})

const totalSalary1 = computed(
  () => period1Shifts.value.reduce((sum, s) => sum + shiftHours(s) * salaryRate.value, 0)
)
const totalBonus1 = computed(
  () => period1Shifts.value.reduce((sum, s) => sum + shiftBonus(s), 0)
)
const totalSalary2 = computed(
  () => period2Shifts.value.reduce((sum, s) => sum + shiftHours(s) * salaryRate.value, 0)
)
const totalBonus2 = computed(
  () => period2Shifts.value.reduce((sum, s) => sum + shiftBonus(s), 0)
)

// Утилиты
function formatDay(date) {
  return date.getDate()
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('ru-RU')
}

function getShiftName(type) {
  return shiftTemplates.find(t => t.type === type)?.name || ''
}

function formatTime(t) {
  return t.slice(0, 5)
}

// Редактирование/удаление
const modalOpen = ref(false)
const modalShift = ref(null)

function openEditModal(shift) {
  modalShift.value = { ...shift }
  modalOpen.value = true
}
function closeModal() {
  modalOpen.value = false
}
function onShiftSaved() {
  modalOpen.value = false
  reloadWithPeriod()
}

function deleteShift(shift) {
  if (!confirm('Удалить смену?')) return
  router.delete(`/shifts/${shift.id}`, {
    onSuccess: () => {
      reloadWithPeriod()
    }
  })
}

// Быстрое добавление смены по шаблону из календаря
function addShiftByTemplate(date, templateType) {
  const tpl = shiftTemplates.find(t => t.type === templateType)
  if (!tpl) return
  let dateStr = ''
  if (typeof date === 'string') {
    dateStr = date
  } else {
    dateStr = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0')
  }
  router.post('/shifts', {
    date: dateStr,
    type: tpl.type,
    start_time: tpl.start_time,
    end_time: tpl.end_time,
    notes: '',
    year: activeYear.value,
    month: activeMonth.value + 1,
    isFirstPeriod: isFirstPeriod.value
  }, {
    onSuccess: () => {
      reloadWithPeriod()
    }
  })
}

// Кнопка "Добавить премию" — подставляет даты текущего периода
function openBonusModal() {
  if (isFirstPeriod.value) {
    // 30–14
    const y = activeYear.value
    const m = activeMonth.value
    const prevMonth = m === 0 ? 11 : m - 1
    const prevYear = m === 0 ? y - 1 : y
    let startDay = 30
    const lastDayPrevMonth = new Date(prevYear, prevMonth + 1, 0).getDate()
    if (startDay > lastDayPrevMonth) startDay = lastDayPrevMonth
    bonusStart.value = new Date(prevYear, prevMonth, startDay).toISOString().slice(0, 10)
    bonusEnd.value = new Date(y, m, 14).toISOString().slice(0, 10)
  } else {
    // 15–29
    const y = activeYear.value
    const m = activeMonth.value
    bonusStart.value = new Date(y, m, 15).toISOString().slice(0, 10)
    bonusEnd.value = new Date(y, m, 29).toISOString().slice(0, 10)
  }
  bonusModalOpen.value = true
}

function shiftHours(s) {
  const start = new Date(`1970-01-01T${s.start_time}`)
  let end = new Date(`1970-01-01T${s.end_time}`)
  if (s.end_time <= s.start_time) {
    end.setDate(end.getDate() + 1)
  }
  return (end - start) / 3600000
}
</script>

<style>
/* доп. стили по необходимости */
</style>
