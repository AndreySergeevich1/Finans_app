<template>
  <Head title="Редактировать обязательный платеж" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Редактировать обязательный платеж
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

              <div>
                <label class="flex items-center">
                  <Checkbox name="is_paid" v-model:checked="form.is_paid" />
                  <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Оплачено</span>
                </label>
              </div>

              <div class="flex items-center justify-between">
                <DangerButton
                  type="button"
                  @click="confirmDelete"
                  class="mr-4"
                >
                  Удалить
                </DangerButton>

                <div class="flex items-center">
                  <Link
                    :href="route('mandatory-payments.index')"
                    class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                  >
                    Отмена
                  </Link>
                  <PrimaryButton :disabled="form.processing">
                    Сохранить
                  </PrimaryButton>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="confirmingDelete" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          Вы уверены, что хотите удалить этот платеж?
        </h2>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal">Отмена</SecondaryButton>

          <DangerButton
            class="ml-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="deletePayment"
          >
            Удалить
          </DangerButton>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
  payment: {
    type: Object,
    required: true
  },
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
  name: props.payment.name,
  amount: props.payment.amount,
  currency: props.payment.currency,
  due_date: props.payment.due_date,
  description: props.payment.description,
  category_id: props.payment.category_id,
  account_id: props.payment.account_id,
  is_paid: props.payment.is_paid
});

const confirmingDelete = ref(false);

const confirmDelete = () => {
  confirmingDelete.value = true;
};

const closeModal = () => {
  confirmingDelete.value = false;
};

const deletePayment = () => {
  form.delete(route('mandatory-payments.destroy', props.payment.id), {
    onSuccess: () => closeModal(),
  });
};

const submit = () => {
  form.put(route('mandatory-payments.update', props.payment.id));
};
</script> 