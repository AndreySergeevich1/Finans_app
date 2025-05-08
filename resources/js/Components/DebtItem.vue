<script setup>
import { ref, computed } from 'vue';
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline';
import ScaleIcon from '@/Components/Icons/ScaleIcon.vue';
import DotsVerticalIcon from '@/Components/Icons/DotsVerticalIcon.vue';

const props = defineProps({
  debt: { type: Object, required: true }
});

// меню
const menuOpen = ref(false);
const toggleMenu = () => menuOpen.value = !menuOpen.value;

// форматеры
const formatCurrency = (v) => {
  const num = Number(v) || 0;
  return new Intl.NumberFormat('ru-RU', { style: 'decimal', minimumFractionDigits: 2 }).format(num);
};

const formatDate = (d) => {
  if (!d) return 'Нет срока';
  const date = new Date(d);
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);

  if (date.toDateString() === today.toDateString()) {
    return 'Сегодня';
  } else if (date.toDateString() === tomorrow.toDateString()) {
    return 'Завтра';
  }
  return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

// лейблы
const debtTypeLabel = computed(() => {
  const m = { loan: 'Кредит', microloan: 'Микрокредит', debt_given: 'Дал в долг', debt_taken: 'Взял в долг' };
  return m[props.debt.type] || 'Долг';
});

const statusLabel = computed(() => {
  const m = { active: 'Активен', paid: 'Погашен', overdue: 'Просрочен', extended: 'Продлен' };
  return m[props.debt.status] || 'Неизвестно';
});

const statusBadgeClass = computed(() => {
  switch (props.debt.status) {
    case 'active':   return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
    case 'paid':     return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    case 'overdue':  return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    case 'extended': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    default:         return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  }
});

const getProgressText = computed(() => {
  if (!props.debt.initial_amount || !props.debt.remaining_amount) {
    return '0% оплачено';
  }
  const progress = ((props.debt.initial_amount - props.debt.remaining_amount) / props.debt.initial_amount) * 100;
  return `${progress.toFixed(1)}% оплачено`;
});

const formatAmount = (amount) => {
  if (amount === undefined || amount === null) {
    return '0.00';
  }
  return Number(amount).toLocaleString('default', {
    style: 'currency',
    currency: props.debt.currency || 'RUB'
  });
};

const getDebtTitle = computed(() => {
  return props.debt.lender_or_borrower || 'Без названия';
});
</script>

<template>
  <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg shadow min-w-0">
    <div class="flex items-center space-x-4 min-w-0">
      <div class="p-2 rounded-full bg-blue-100 text-blue-600 shrink-0">
        <ScaleIcon class="h-5 w-5" />
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center justify-between min-w-0">
          <h4 class="font-medium text-base truncate max-w-[160px]">{{ getDebtTitle }}</h4>
          <span class="font-semibold text-lg text-right text-gray-900 dark:text-gray-100 ml-4 shrink-0">{{ formatAmount(debt.remaining_amount) }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
          <span :class="'px-2 py-0.5 rounded-full font-semibold ' + (debtTypeLabel === 'Взял в долг' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700')">{{ debtTypeLabel }}</span>
          <span v-if="debt.due_date" class="text-gray-500 dark:text-gray-400">Срок: {{ formatDate(debt.due_date) }}</span>
          <span v-else class="text-gray-400">Без срока</span>
          <span :class="'px-2 py-0.5 rounded-full font-semibold ' + statusBadgeClass">{{ statusLabel }}</span>
        </div>
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
          {{ getProgressText }}
        </div>
      </div>
    </div>
    <DotsVerticalIcon class="h-5 w-5 text-gray-400 ml-2 shrink-0" />
  </div>
</template>
