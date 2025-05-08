<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue'; // из предыдущей инструкции
import { computed } from 'vue';

// Принимаем данные из контроллера
const props = defineProps({
  analysis: String,
  transactions: Array,
});

// Подготовим данные для круговой диаграммы расходов
const chartData = computed(() => {
  const byCategory = {};
  props.transactions
    .filter(tx => tx.type === 'expense')
    .forEach(tx => {
      byCategory[tx.category] = (byCategory[tx.category] || 0) + tx.amount;
    });

  const labels = Object.keys(byCategory);
  const data = Object.values(byCategory);
  const bg = labels.map((_, i) => {
    const hue = (i * 60) % 360;
    return `hsl(${hue}, 70%, 60%)`;
  });

  return {
    labels,
    datasets: [{ data, backgroundColor: bg, borderWidth: 1 }],
  };
});
</script>

<template>
  <Head title="Анализ бюджета" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
        Аналитика <span class="text-indigo-600">ИИ</span>
      </h2>
    </template>

    <div class="py-8 space-y-8">

      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
        <h3 class="text-lg font-semibold mb-4">Распределение расходов</h3>
        <DoughnutChart :chartData="chartData" />
      </div>


      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
        <h3 class="text-lg font-semibold mb-4">Рекомендации и инсайты</h3>
        <p class="whitespace-pre-wrap text-gray-700 dark:text-gray-300">{{ analysis }}</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
