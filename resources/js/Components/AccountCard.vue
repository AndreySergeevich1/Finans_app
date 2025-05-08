<template>
  <div class="bg-white rounded-xl shadow-md p-4 mb-4 flex items-center justify-between transition hover:shadow-lg">
    <div class="flex items-center">
      <span class="mr-4 p-3 rounded-full" :class="iconBackground">
        <component :is="iconComponent" class="w-6 h-6" :class="iconColor" />
      </span>
      <div>
        <div class="font-semibold text-gray-800">{{ account.name }}</div>
        <div class="text-sm text-gray-500">{{ accountTypeLabel(account.type) }}</div>
      </div>
    </div>
    <div class="text-right">
       <div class="font-bold text-lg" :class="balanceColor(account.balance)">
         {{ formatCurrency(account.balance, account.currency) }}
       </div>
       <div class="text-xs text-gray-400">{{ account.currency }}</div>
    </div>
     </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
// Пример импорта иконок из Heroicons (установите @heroicons/vue)
import { BanknotesIcon, CreditCardIcon, CurrencyDollarIcon, BuildingLibraryIcon, QuestionMarkCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  account: {
    type: Object,
    required: true,
  },
});

const formatCurrency = (value, currency) => {
    return new Intl.NumberFormat('ru-RU', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
    // Или для знака валюты:
    // return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: currency }).format(value);
};

const accountTypeLabel = (type) => {
  const types = { bank_account: 'Банк. счет', card: 'Карта', cash: 'Наличные', savings: 'Вклад', other: 'Другое' };
  return types[type] || 'Неизвестно';
};

const balanceColor = (balance) => {
    return parseFloat(balance) >= 0 ? 'text-green-600' : 'text-red-600';
};

// Логика выбора иконки и ее фона (пример)
const iconComponent = computed(() => {
    switch (props.account.type) {
        case 'bank_account': return BuildingLibraryIcon;
        case 'card': return CreditCardIcon;
        case 'cash': return CurrencyDollarIcon;
        case 'savings': return BanknotesIcon;
        default: return QuestionMarkCircleIcon;
    }
});

const iconBackground = computed(() => {
     // Можно задать разные цвета фона в зависимости от типа счета или account.color из БД
    return 'bg-indigo-100';
});
const iconColor = computed(() => {
    // Можно задать разные цвета иконки
    return 'text-indigo-600';
});

</script>
