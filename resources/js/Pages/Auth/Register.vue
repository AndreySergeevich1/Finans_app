<template>
  <GuestLayout>
    <template #header>
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Создать аккаунт</h2>
    </template>
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <InputLabel for="name" value="Имя" />
        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>
      <div>
        <InputLabel for="email" value="Email" />
        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
        <InputError :message="form.errors.email" class="mt-2" />
      </div>
      <div>
        <InputLabel for="password" value="Пароль" />
        <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
        <InputError :message="form.errors.password" class="mt-2" />
      </div>
      <div>
        <InputLabel for="password_confirmation" value="Повторите пароль" />
        <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
        <InputError :message="form.errors.password_confirmation" class="mt-2" />
      </div>
      <div>
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full">
          Зарегистрироваться
        </PrimaryButton>
      </div>
    </form>
    <div class="mt-4 text-center text-sm text-gray-600">
      Уже есть аккаунт? <Link href="/login" class="text-indigo-600 hover:underline">Войти</Link>
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

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
function submit() {
  form.post(route('register'));
}
</script>
