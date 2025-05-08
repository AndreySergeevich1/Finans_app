<template>
  <Head title="Добавить обязательный платеж" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Добавить обязательный платеж
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <InputLabel for="name" value="Название" />
                <TextInput
                  id="name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.name"
                  required
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <div>
                <InputLabel for="amount" value="Сумма" />
                <TextInput
                  id="amount"
                  type="number"
                  step="0.01"
                  class="mt-1 block w-full"
                  v-model="form.amount"
                  required
                />
                <InputError :message="form.errors.amount" class="mt-2" />
              </div>

              <div>
                <InputLabel for="currency" value="Валюта" />
                <SelectInput
                  id="currency"
                  class="mt-1 block w-full"
                  v-model="form.currency"
                  required
                >
                  <option value="">Выберите валюту</option>
                  <option
                    v-for="currency in currencies"
                    :key="currency.code"
                    :value="currency.code"
                  >
                    {{ currency.code }} ({{ currency.symbol }}) - {{ currency.name }}
                  </option>
                </SelectInput>
                <InputError :message="form.errors.currency" class="mt-2" />
              </div>

              <div>
                <InputLabel for="due_date" value="День платежа" />
                <TextInput
                  id="due_date"
                  type="number"
                  min="1"
                  max="31"
                  class="mt-1 block w-full"
                  v-model="form.due_date"
                  required
                />
                <InputError :message="form.errors.due_date" class="mt-2" />
              </div>

              <div>
                <InputLabel for="description" value="Описание" />
                <TextArea
                  id="description"
                  class="mt-1 block w-full"
                  v-model="form.description"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <div>
                <InputLabel for="category_id" value="Категория" />
                <SelectInput
                  id="category_id"
                  class="mt-1 block w-full"
                  v-model="form.category_id"
                >
                  <option value="">Выберите категорию</option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    <div class="flex items-center">
                      <div class="w-3 h-3 rounded-full mr-2" :style="{ backgroundColor: category.color }"></div>
                      {{ category.name }} ({{ category.type === 'expense' ? 'Расход' : 'Доход' }})
                    </div>
                  </option>
                </SelectInput>
                <InputError :message="form.errors.category_id" class="mt-2" />
              </div>

              <div>
                <InputLabel for="account_id" value="Счет" />
                <SelectInput
                  id="account_id"
                  class="mt-1 block w-full"
                  v-model="form.account_id"
                >
                  <option value="">Выберите счет</option>
                  <option
                    v-for="account in accounts"
                    :key="account.id"
                    :value="account.id"
                  >
                    {{ account.name }} ({{ account.currency }})
                  </option>
                </SelectInput>
                <InputError :message="form.errors.account_id" class="mt-2" />
              </div>

              <div class="flex items-center justify-end">
                <Link
                  :href="route('mandatory-payments.index')"
                  class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                >
                  Отмена
                </Link>
                <PrimaryButton :disabled="form.processing">
                  Добавить
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
  accounts: {
    type: Array,
    required: true
  },
  categories: {
    type: Array,
    required: true
  },
  currencies: {
    type: Array,
    required: true
  }
});

const form = useForm({
  name: '',
  amount: '',
  currency: '',
  due_date: '',
  description: '',
  category_id: '',
  account_id: ''
});

const submit = () => {
  form.post(route('mandatory-payments.store'));
};
</script> 