<template>
  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-6">Меню профиля</h3>
        <div class="grid grid-cols-1 gap-4">
          <Link href="/profile/edit" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">
            🔒 Безопасность (имя, e-mail, пароль)
          </Link>
          <Link href="/shifts" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">
            📅 График смен
          </Link>
          <Link href="/categories" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">
            🏷️ Редактирование категорий
          </Link>
          <Link href="/currencies" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">
            💱 Редактирование валют
          </Link>
          <Link href="/profile/rate" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">
            💸 Редактирование ставки за смену и премии
          </Link>
          <form @submit.prevent="logout" class="mt-2">
            <button type="submit" class="w-full p-4 rounded bg-red-100 hover:bg-red-200 text-red-700 font-semibold transition">
              🚪 Выйти
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

function logout() {
  router.post('/logout')
}
</script> 