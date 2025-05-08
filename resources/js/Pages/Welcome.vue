<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const loading = ref(true);
const showCard = ref(false);
const showContent = ref(false);

onMounted(() => {
    // Эмулируем загрузку
    setTimeout(() => {
        loading.value = false;
        showCard.value = true;
    }, 700); // Спиннер будет крутиться 0.7 секунды

    setTimeout(() => {
        showContent.value = true;
    }, 1000); // Текст появится чуть позже
});
</script>

<template>
  <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500 px-6">
    <div v-if="loading" class="flex justify-center items-center">
      <div class="loader"></div>
    </div>

    <div
      v-else
      class="transition-all duration-700 transform scale-95 opacity-0 animate-fadeInUp max-w-md w-full text-center bg-white p-8 rounded-3xl shadow-2xl"
    >
      <div v-if="showContent" class="transition-all duration-500">
        <h1 class="text-3xl font-bold text-gray-800">Добро пожаловать 👋</h1>
        <p class="mt-2 text-gray-500">Управляйте своими финансами легко и просто</p>

        <div class="space-y-4 mt-8">
          <Link
            href="/login"
            class="block w-full py-3 text-white bg-purple-600 hover:bg-purple-700 rounded-xl text-lg font-semibold transition"
          >
            Войти
          </Link>
          <Link
            href="/register"
            class="block w-full py-3 text-purple-600 bg-white border-2 border-purple-600 hover:bg-purple-50 rounded-xl text-lg font-semibold transition"
          >
            Зарегистрироваться
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(40px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.7s ease-out forwards;
}

/* Спиннер */
.loader {
  border: 6px solid #f3f3f3;
  border-top: 6px solid #9333ea; /* Фиолетовый */
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
  margin: auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
