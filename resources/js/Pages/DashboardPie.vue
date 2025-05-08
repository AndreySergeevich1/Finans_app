<template>
  <Card class="flex flex-col">
    <CardHeader class="items-center pb-0">
      <CardTitle>Доходы и расходы</CardTitle>
      <CardDescription>{{ monthLabel }}</CardDescription>
    </CardHeader>
    <CardContent class="flex-1 pb-0 relative">
      <Doughnut :data="chartData" :options="chartOptions" class="mx-auto max-h-[250px]" />
      <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
        <span class="text-3xl font-bold">{{ total.toLocaleString() }}</span>
        <span class="text-muted-foreground">Всего</span>
      </div>
    </CardContent>
    <CardFooter class="flex-col gap-2 text-sm">
      <div class="flex items-center gap-2 font-medium leading-none">
        Тренд за месяц <TrendingUp class="h-4 w-4" />
      </div>
      <div class="leading-none text-muted-foreground">Доходы и расходы за {{ monthLabel }}</div>
    </CardFooter>
  </Card>
</template>

<script setup>
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { TrendingUp } from 'lucide-vue-next'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
  income: { type: Number, default: 0 },
  expense: { type: Number, default: 0 },
  monthLabel: { type: String, default: 'Май 2024' }
})

const chartData = computed(() => ({
  labels: ['Доходы', 'Расходы'],
  datasets: [
    {
      data: [props.income, props.expense],
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

const total = computed(() => props.income + props.expense)
</script> 