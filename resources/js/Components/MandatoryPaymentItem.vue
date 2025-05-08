<script setup>
import { ref, computed } from 'vue';
import { CalendarIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  payment: { type: Object, required: true }
});

const formatDate = (d) => {
  if (!d) return 'Нет срока';
  return `${d} число`;
};

const formatAmount = (amount) => {
  if (amount === undefined || amount === null) {
    return '0.00';
  }
  return Number(amount).toLocaleString('default', {
    style: 'currency',
    currency: props.payment.currency || 'RUB'
  });
};

const getDaysUntilPayment = computed(() => {
  if (!props.payment.due_date) return null;
  const today = new Date();
  const currentDay = today.getDate();
  const dueDay = parseInt(props.payment.due_date);
  
  let diffDays = dueDay - currentDay;
  if (diffDays < 0) {
    const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
    diffDays += daysInMonth;
  }
  
  return diffDays;
});

const getStatusClass = computed(() => {
  const days = getDaysUntilPayment.value;
  if (days === null) return 'text-gray-500';
  if (days < 0) return 'text-red-500';
  if (days <= 3) return 'text-orange-500';
  return 'text-green-500';
});

const getStatusText = computed(() => {
  const days = getDaysUntilPayment.value;
  if (days === null) return 'Нет срока';
  if (days === 0) return 'Сегодня';
  if (days === 1) return 'Завтра';
  if (days < 0) return 'Просрочено';
  return `Через ${days} дн.`;
});
</script>

<template>
  <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg shadow min-w-0">
    <div class="flex items-center space-x-4 min-w-0">
      <div class="p-2 rounded-full bg-purple-100 text-purple-600 shrink-0">
        <CalendarIcon class="h-5 w-5" />
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center justify-between min-w-0">
          <h4 class="font-medium text-base truncate max-w-[160px]">{{ payment.name }}</h4>
          <span class="font-semibold text-lg text-right text-gray-900 dark:text-gray-100 ml-4 shrink-0">{{ formatAmount(payment.amount) }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
          <span v-if="payment.category" class="px-2 py-0.5 rounded-full font-semibold bg-indigo-100 text-indigo-700">{{ payment.category.name }}</span>
          <span v-if="payment.account" class="text-gray-500 dark:text-gray-400">({{ payment.account.name }})</span>
          <span class="text-gray-500 dark:text-gray-400">{{ payment.currency }}</span>
          <span :class="'px-2 py-0.5 rounded-full font-semibold ' + getStatusClass">{{ getStatusText }}</span>
        </div>
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
          <span>{{ formatDate(payment.due_date) }}</span>
          <span v-if="payment.description" class="truncate max-w-[100px]">{{ payment.description }}</span>
        </div>
      </div>
    </div>
  </div>
</template> 