<template>
  <GuestLayout>
    <template #header>
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Войти в аккаунт</h2>
    </template>
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <InputLabel for="email" value="Email" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="username"
        />
        <InputError :message="form.errors.email" class="mt-2" />
      </div>
      <div>
        <InputLabel for="password" value="Пароль" />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="current-password"
        />
        <InputError :message="form.errors.password" class="mt-2" />
      </div>
      <div class="flex items-center justify-between">
        <label class="flex items-center">
          <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
          <span class="ml-2 text-sm text-gray-600">Запомнить меня</span>
        </label>
        <Link href="/forgot-password" class="text-sm text-indigo-600 hover:underline">Забыли пароль?</Link>
      </div>
      <div>
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full">
          Войти
        </PrimaryButton>
      </div>
    </form>
    <div class="mt-4 text-center text-sm text-gray-600">
      Нет аккаунта? <Link href="/register" class="text-indigo-600 hover:underline">Зарегистрироваться</Link>
    </div>
  </GuestLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '', remember: false });
function submit() {
  form.post(route('login'), { onFinish: () => form.reset('password') });
}
</script>
