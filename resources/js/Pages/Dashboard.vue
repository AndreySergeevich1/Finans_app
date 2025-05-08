<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <!-- Слот header -->
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Привет, {{ auth.user.name }}!
      </h2>
    </template>

    <!-- Основной контент -->
    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- Левая колонка -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Баланс и выбор счета -->
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg relative">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="text-lg font-semibold opacity-90">{{ displayName }}</h3>
                  <p class="text-sm opacity-80">{{ displayAccountTypeLabel }}</p>
                </div>
                <div v-if="accounts.length > 0" class="relative ml-4">
                  <select
                    v-model="selectedAccountId"
                    class="appearance-none bg-white/20 hover:bg-white/30 text-white text-xs font-medium rounded-full py-1 pl-3 pr-6 cursor-pointer focus:outline-none focus:ring-2 focus:ring-white/50 transition-colors"
                    aria-label="Выбрать счет"
                  >
                    <option value="all" class="text-black">Все счета</option>
                    <option
                      v-for="account in accounts"
                      :key="account.id"
                      :value="account.id"
                      class="text-black"
                    >
                      {{ account.name }}
                    </option>
                  </select>
                  <ChevronDownIcon class="w-3 h-3 text-white absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none" />
                </div>
                <div v-else class="text-xs opacity-70">
                  Нет счетов
                </div>
              </div>

              <p class="text-4xl font-bold tracking-tight mb-1">
                {{ formatCurrencyWithSymbol(displayBalance, displayCurrency) }}
              </p>

              <div class="mt-4 text-right">
                <Link
                  :href="selectedAccount
                    ? route('accounts.edit', selectedAccount.id)
                    : route('accounts.index')"
                  class="text-sm font-medium hover:underline opacity-90"
                >
                  {{ selectedAccount ? 'Детали счета' : 'Все счета' }} &rarr;
                </Link>
              </div>
            </div>
            <!-- /Баланс и выбор счета -->

            <!-- Обзор за месяц -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Обзор за месяц</h3>
                <Link
                  :href="route('profile.edit')"
                  class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400"
                  title="Настроить лимит в профиле"
                >
                  <Cog6ToothIcon class="w-5 h-5"/>
                </Link>
              </div>

              <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700/50 rounded-lg p-3">
                  <div class="flex items-center text-sm text-green-700 dark:text-green-400 mb-1">
                    <ArrowTrendingUpIcon class="w-4 h-4 mr-1"/> Доход
                  </div>
                  <p class="text-lg font-semibold">
                    {{ formatCurrencyWithSymbol(filteredMonthlyIncome, displayCurrency) }}
                  </p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700/50 rounded-lg p-3">
                  <div class="flex items-center text-sm text-red-700 dark:text-red-400 mb-1">
                    <ArrowTrendingDownIcon class="w-4 h-4 mr-1"/> Расход
                  </div>
                  <p class="text-lg font-semibold">
                    {{ formatCurrencyWithSymbol(filteredMonthlyExpenses, displayCurrency) }}
                  </p>
                </div>
              </div>

              <!-- Диаграммы -->
              <div class="my-6">
                <div class="flex flex-col items-center">
                  <Doughnut :data="chartData" :options="chartOptions" class="mx-auto max-h-[250px]" />
                </div>
              </div>
              <!-- /Диаграммы -->

            </div>
            <!-- /Обзор за месяц -->

            <!-- Последние транзакции -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Последние транзакции</h3>
                <Link
                  :href="route('transactions.index')"
                  class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  Все транзакции &rarr;
                </Link>
              </div>
              <div class="space-y-4">
                <div v-if="recentTransactions.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                  Нет последних транзакций
                </div>
                <template v-else>
                  <TransactionItem
                    v-for="transaction in recentTransactions"
                    :key="transaction.id"
                    :transaction="transaction"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                    @click="goToTransaction(transaction.id)"
                  />
                </template>
              </div>
            </div>
            <!-- /Последние транзакции -->

          </div>
          <!-- /Левая колонка -->

          <!-- Правая колонка -->
          <div class="space-y-6">

            <!-- Обязательные платежи -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
              <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <CalendarIcon class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"/> Обязательные платежи
              </h3>
              <div class="space-y-3">
                <div v-if="mandatoryPayments.length === 0 && debtPayments.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                  Нет обязательных платежей
                </div>
                <template v-else>
                  <div v-if="mandatoryPayments.length > 0">
                    <div class="flex items-center mb-1">
                      <CalendarIcon class="w-4 h-4 mr-1 text-purple-500" />
                      <span class="font-semibold text-sm text-gray-700 dark:text-gray-300">Обязательные платежи</span>
                    </div>
                    <MandatoryPaymentItem
                      v-for="payment in mandatoryPayments"
                      :key="payment.id"
                      :payment="payment"
                      class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                      @click="goToPayment(payment.id)"
                    />
                  </div>
                  <div v-if="debtPayments.length > 0">
                    <div class="flex items-center mt-3 mb-1">
                      <ScaleIcon class="w-4 h-4 mr-1 text-orange-500" />
                      <span class="font-semibold text-sm text-gray-700 dark:text-gray-300">Платежи по долгам</span>
                    </div>
                    <MandatoryPaymentItem
                      v-for="payment in debtPayments"
                      :key="'debt-' + payment.id"
                      :payment="payment"
                      class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                      @click="goToDebt(payment.id)"
                    />
                  </div>
                </template>
              </div>
              <div class="mt-4 text-right">
                <Link
                  :href="route('mandatory-payments.index')"
                  class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  Все платежи &rarr;
                </Link>
              </div>
            </div>
            <!-- /Обязательные платежи -->

            <!-- Долги -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
              <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <ScaleIcon class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400"/> Мои долги
              </h3>
              <div class="space-y-3">
                <div v-if="debts.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                  У вас нет активных долгов.
                </div>
                <template v-else>
                  <DebtItem
                    v-for="debt in debts"
                    :key="debt.id"
                    :debt="debt"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                    @click="goToDebt(debt.id)"
                  />
                </template>
              </div>
              <div class="mt-4 text-right">
                <Link
                  :href="route('debts.index')"
                  class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  Все долги &rarr;
                </Link>
              </div>
            </div>
            <!-- /Долги -->

          </div>
          <!-- /Правая колонка -->

        </div>
      </div>
    </div>
    <!-- /Основной контент -->

  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionItem from '@/Components/TransactionItem.vue';
import DebtItem from '@/Components/DebtItem.vue';
import MandatoryPaymentItem from '@/Components/MandatoryPaymentItem.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import { Cog6ToothIcon, ArrowTrendingUpIcon, ArrowTrendingDownIcon, ChevronDownIcon, CalendarIcon, ScaleIcon } from '@heroicons/vue/24/outline';
import { Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
ChartJS.register(ArcElement, Tooltip, Legend)

// Деструктурируем пропсы
const {
  auth,
  accounts = [],
  totalBalance: totalBalanceRaw = 0,
  monthlyLimit: monthlyLimitRaw = 0,
  monthlyIncome: monthlyIncomeRaw = 0,
  monthlyExpenses: monthlyExpensesRaw = 0,
  recentTransactions = [],
  transactions = [],
  debts = [],
  mandatoryPayments = [],
  debtPayments = [],
} = defineProps({
  auth: Object,
  accounts: Array,
  totalBalance: [Number, String],
  monthlyLimit: [Number, String],
  monthlyIncome: [Number, String],
  monthlyExpenses: [Number, String],
  recentTransactions: Array,
  transactions: Array,
  debts: Array,
  mandatoryPayments: Array,
  debtPayments: Array,
});

// Приводим строковые значения к числам
const totalBalance    = Number(totalBalanceRaw);
const monthlyLimit    = Number(monthlyLimitRaw);
const monthlyIncome   = Number(monthlyIncomeRaw);
const monthlyExpenses = Number(monthlyExpensesRaw);

// Выбор счета
const selectedAccountId = ref(accounts.length > 0 ? 'all' : null);
const selectedAccount = computed(() =>
  selectedAccountId.value === 'all'
    ? null
    : accounts.find(acc => acc.id === selectedAccountId.value) || null
);

// Отображаемый баланс и валюта
const displayBalance = computed(() =>
  selectedAccountId.value === 'all'
    ? totalBalance
    : (selectedAccount.value?.balance || 0)
);
const displayCurrency = computed(() =>
  selectedAccountId.value === 'all'
    ? 'RUB'
    : (selectedAccount.value?.currency || 'RUB')
);
const displayName = computed(() =>
  selectedAccountId.value === 'all'
    ? 'Общий баланс'
    : (selectedAccount.value?.name || 'Счет не найден')
);
const displayAccountTypeLabel = computed(() => {
  if (selectedAccountId.value === 'all') return 'по всем счетам';
  const type = selectedAccount.value?.type;
  const map = { bank_account: 'Банк. счет', card: 'Карта', cash: 'Наличные', savings: 'Вклад', other: 'Другое' };
  return map[type] || '';
});

// Функции для навигации
const goToTransaction = (id) => {
  router.visit(route('transactions.edit', id));
};

const goToPayment = (id) => {
  router.visit(route('mandatory-payments.edit', id));
};

const goToDebt = (id) => {
  router.visit(route('debts.edit', id));
};

// Форматирование
const formatCurrencyWithSymbol = (value, currency = 'RUB') =>
  new Intl.NumberFormat('ru-RU', { style: 'currency', currency, minimumFractionDigits: 2 }).format(value || 0);

// Фильтрация транзакций
const filteredTransactions = computed(() =>
  selectedAccountId.value === 'all'
    ? recentTransactions
    : recentTransactions.filter(tx => tx.account_id === selectedAccountId.value)
);
const filteredMonthlyIncome = computed(() =>
  filteredTransactions.value.filter(tx => tx.type === 'income').reduce((s, tx) => s + tx.amount, 0)
);
const filteredMonthlyExpenses = computed(() =>
  filteredTransactions.value.filter(tx => tx.type === 'expense').reduce((s, tx) => s + tx.amount, 0)
);

// Chart Data
const expensesChartData = computed(() => {
  const cats = {};
  transactions.forEach(tx => {
    if (tx.type === 'expense' && tx.category) {
      const n = tx.category.name || 'Без категории';
      cats[n] = (cats[n] || 0) + tx.amount;
    }
  });
  return { labels: Object.keys(cats), datasets: [{ data: Object.values(cats), backgroundColor: [] }] };
});

const incomeExpensesChartData = computed(() => {
  const months = {};
  transactions.forEach(tx => {
    const m = new Date(tx.transaction_date).toLocaleString('default', { month: 'short' });
    months[m] = months[m] || { income: 0, expense: 0 };
    months[m][tx.type === 'income' ? 'income' : 'expense'] += tx.amount;
  });
  return {
    labels: Object.keys(months),
    datasets: [
      { label: 'Доход', data: Object.values(months).map(d => d.income), backgroundColor: [] },
      { label: 'Расходы', data: Object.values(months).map(d => d.expense), backgroundColor: [] },
    ]
  };
});

const chartData = computed(() => ({
  labels: ['Доходы', 'Расходы'],
  datasets: [
    {
      data: [filteredMonthlyIncome.value, filteredMonthlyExpenses.value],
      backgroundColor: ['#22c55e', '#ef4444'],
      borderWidth: 2,
    }
  ]
}))
const chartOptions = {
  cutout: '70%',
  plugins: {
    legend: { display: true, position: 'bottom' },
    tooltip: { enabled: true }
  }
}
const total = computed(() => filteredMonthlyIncome.value + filteredMonthlyExpenses.value)

</script>
