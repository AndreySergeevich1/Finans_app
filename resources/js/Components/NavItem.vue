
<template>
  <Link
    :href="href"
    :class="[isActive ? activeClass : inactiveClass, 'group flex items-center px-4 py-2 rounded-md transition-colors']"
  >
    <component :is="iconComponent" class="w-5 h-5 mr-3" />
    <span class="flex-1 text-sm font-medium">{{ label }}</span>
  </Link>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  WalletIcon,
  PresentationChartLineIcon,
  ArrowTrendingUpIcon,
  ScaleIcon,
  Cog6ToothIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  href: { type: String, required: true },
  icon: { type: String, required: true },
  label: { type: String, required: true }
});

// Map icon name to component
const icons = {
  WalletIcon,
  PresentationChartLineIcon,
  ArrowTrendingUpIcon,
  ScaleIcon,
  Cog6ToothIcon
};
const iconComponent = icons[props.icon] || WalletIcon;

// Active if URL starts with this href
const page = usePage();
const isActive = computed(() => page.props.url.startsWith(props.href));

const activeClass =
  'bg-indigo-100 dark:bg-gray-800 text-indigo-700 dark:text-indigo-200';
const inactiveClass =
  'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white';
</script>
