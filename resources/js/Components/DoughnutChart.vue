<script setup>
import { Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps({
    chartData: { type: Object, required: true },
    chartOptions: { type: Object, default: () => ({ responsive: true, maintainAspectRatio: false }) }
})
</script>

<template>
  <div class="h-48">
    <Doughnut :data="chartData" :options="chartOptions" />
  </div>
</template>
