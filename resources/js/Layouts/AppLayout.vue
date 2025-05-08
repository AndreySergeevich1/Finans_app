<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const isLoading = ref(false);

// Обработка загрузки
router.on('start', () => {
  isLoading.value = true;
});
router.on('finish', () => {
  isLoading.value = false;
});
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <transition name="fade" mode="out-in">
      <slot />
    </transition>

    <transition name="fade">
      <div v-if="isLoading" class="fixed inset-0 bg-white/80 z-50 flex items-center justify-center">
        <div class="loader"></div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.loader {
  border: 6px solid #f3f3f3;
  border-top: 6px solid #9333ea;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
