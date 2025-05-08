<script setup>
import { ref, computed } from 'vue';
import { ArrowDownTrayIcon, ArrowUpTrayIcon, ShoppingCartIcon, QuestionMarkCircleIcon, BuildingOfficeIcon, BanknotesIcon, TruckIcon, TvIcon, TicketIcon } from '@heroicons/vue/24/outline';
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline';
import DotsVerticalIcon from '@/Components/Icons/DotsVerticalIcon.vue';

const props = defineProps({
  transaction: { type: Object, required: true }
});

// меню
const menuOpen = ref(false);
const toggleMenu = () => menuOpen.value = !menuOpen.value;

// форматеры
const formatCurrency = (value, currency = 'RUB') => {
  const num = Number(value) || 0;
  return new Intl.NumberFormat('ru-RU', { style: 'decimal', minimumFractionDigits: 2 }).format(num);
};
const formatDate = (d) => d
  ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: 'short' })
  : '';

// иконка категории
const categoryIcon = computed(() => {
  const cat = props.transaction.category?.name?.toLowerCase() || '';
  if (props.transaction.type === 'income') return ArrowUpTrayIcon;
  if (cat.includes('транспорт'))      return TruckIcon;
  if (cat.includes('продукт') || cat.includes('еда')) return ShoppingCartIcon;
  if (cat.includes('жкх') || cat.includes('комм'))  return BuildingOfficeIcon;
  if (cat.includes('развлечен') || cat.includes('кино')) return TicketIcon;
  if (cat.includes('интернет') || cat.includes('связь')) return TvIcon;
  return ArrowDownTrayIcon;
});

const amountColor = computed(() =>
  props.transaction.type === 'income'
    ? 'text-green-600 dark:text-green-400 font-semibold'
    : 'text-gray-700 dark:text-gray-300'
);

const amountPrefix = computed(() =>
  props.transaction.type === 'income' ? '+' : '-'
);

const getCategoryColor = computed(() => {
  if (!props.transaction.category) {
    return 'bg-gray-100 text-gray-600';
  }
  return `bg-${props.transaction.category.color}-100 text-${props.transaction.category.color}-600`;
});

const getAmountColor = computed(() => {
  return props.transaction.type === 'income' 
    ? 'text-green-600 dark:text-green-400' 
    : 'text-red-600 dark:text-red-400';
});

const formatAmount = (amount) => {
  const prefix = props.transaction.type === 'income' ? '+' : '-';
  return `${prefix}${Math.abs(amount).toLocaleString('default', {
    style: 'currency',
    currency: props.transaction.account.currency
  })}`;
};
</script>

<template>
  <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="flex items-center space-x-4">
      <div :class="getCategoryColor" class="p-2 rounded-full">
        <component :is="categoryIcon" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
      </div>
      <div>
        <h4 class="font-medium">{{ transaction.description }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ transaction.category?.name || 'Без категории' }} • {{ formatDate(transaction.transaction_date) }}
        </p>
      </div>
    </div>
    <div class="flex items-center space-x-4">
      <span :class="getAmountColor" class="font-medium">
        {{ formatAmount(transaction.amount) }}
      </span>
      <DotsVerticalIcon class="h-5 w-5 text-gray-400" />
    </div>
  </div>
</template>
