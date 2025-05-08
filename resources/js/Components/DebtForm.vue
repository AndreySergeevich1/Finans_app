<template>
  <form @submit.prevent="submit" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <div>
        <InputLabel for="type" value="Тип" />
        <select
          id="type"
          v-model="form.type"
          required
          class="mt-1 block w-full form-select"
        >
          <option value="loan">Кредит банка</option>
          <option value="microloan">Микрокредит</option>
          <option value="debt_taken">Я взял в долг</option>
          <option value="debt_given">Я дал в долг</option>
        </select>
        <InputError class="mt-2" :message="form.errors.type" />
      </div>


      <div>
        <InputLabel for="lender_or_borrower" value="Кредитор / Заемщик" />
        <TextInput
          id="lender_or_borrower"
          v-model="form.lender_or_borrower"
          required
          placeholder="Название банка или имя"
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.lender_or_borrower" />
      </div>


      <div>
        <InputLabel for="initial_amount" value="Начальная сумма" />
        <TextInput
          id="initial_amount"
          type="number"
          class="mt-1 block w-full"
          v-model="form.initial_amount"
          required
        />
        <InputError class="mt-2" :message="form.errors.initial_amount" />
      </div>


      <div>
        <InputLabel for="remaining_amount" value="Оставшаяся сумма" />
        <div class="mt-1 block w-full bg-gray-100 rounded px-3 py-2 text-gray-700">
          {{ form.remaining_amount }}
        </div>
        <input type="hidden" v-model="form.remaining_amount" name="remaining_amount" />
        <InputError class="mt-2" :message="form.errors.remaining_amount" />
      </div>


      <div>
        <InputLabel for="currency" value="Валюта" />
        <TextInput
          id="currency"
          v-model="form.currency"
          required
          placeholder="RUB"
          maxlength="3"
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.currency" />
      </div>


      <div>
        <InputLabel for="interest_rate" value="Ставка, % годовых" />
        <TextInput
          id="interest_rate"
          type="number"
          step="0.01"
          v-model="form.interest_rate"
          placeholder="15.5"
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.interest_rate" />
      </div>


      <div>
        <InputLabel for="interest_type" value="Тип %" />
        <select
          id="interest_type"
          v-model="form.interest_type"
          class="mt-1 block w-full form-select"
        >
          <option :value="null">-- Не указан --</option>
          <option value="simple">Простой</option>
          <option value="compound">Сложный</option>
        </select>
        <InputError class="mt-2" :message="form.errors.interest_type" />
      </div>


      <div>
        <InputLabel for="due_date" value="Срок погашения" />
        <TextInput
          id="due_date"
          type="date"
          v-model="form.due_date"
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.due_date" />
      </div>


      <div>
        <InputLabel for="status" value="Статус" />
        <select
          id="status"
          v-model="form.status"
          required
          class="mt-1 block w-full form-select"
        >
          <option value="active">Активен</option>
          <option value="paid">Погашен</option>
          <option value="overdue">Просрочен</option>
          <option value="extended">Продлен</option>
        </select>
        <InputError class="mt-2" :message="form.errors.status" />
      </div>


      <div class="md:col-span-2">
        <InputLabel for="notes" value="Заметки" />
        <TextInput
          id="notes"
          type="text"
          v-model="form.notes"
          placeholder="Доп. информация..."
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.notes" />
      </div>

      <div>
        <InputLabel for="start_date" value="Дата взятия" />
        <TextInput
          id="start_date"
          type="date"
          v-model="form.start_date"
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.start_date" />
      </div>

      <div>
        <InputLabel for="payment_date" value="Дата платежа" />
        <TextInput
          id="payment_date"
          type="date"
          v-model="form.payment_date"
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.payment_date" />
      </div>

      <div>
        <InputLabel for="end_date" value="Дата окончания" />
        <TextInput
          id="end_date"
          type="date"
          v-model="form.end_date"
          class="mt-1 block w-full"
        />
        <InputError class="mt-2" :message="form.errors.end_date" />
      </div>

      <div>
        <InputLabel for="final_amount" value="Конечная сумма (расчет)" />
        <TextInput
          id="final_amount"
          type="number"
          v-model="form.final_amount"
          class="mt-1 block w-full"
          readonly
        />
      </div>
    </div>


    <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
      <Link :href="route(cancelRoute)">
        <SecondaryButton type="button">Отмена</SecondaryButton>
      </Link>
      <PrimaryButton :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
        {{ submitButtonText }}
      </PrimaryButton>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Props
const props = defineProps({
  debt: { type: Object, default: null },
  submitRoute: { type: String, required: true },
  cancelRoute: { type: String, required: true },
  submitButtonText: { type: String, default: 'Сохранить' }
});

// Деструктурируем
const { submitRoute, cancelRoute, submitButtonText } = props;

// Флаг редактирования
const isEditing = computed(() => !!props.debt);

// Инициализация формы
const form = useForm({
  type: props.debt?.type ?? '',
  lender_or_borrower: props.debt?.lender_or_borrower ?? '',
  initial_amount: props.debt?.initial_amount ?? '',
  remaining_amount: props.debt?.remaining_amount ?? '',
  currency: props.debt?.currency ?? '',
  interest_rate: props.debt?.interest_rate ?? '',
  interest_type: props.debt?.interest_type ?? '',
  due_date: props.debt?.due_date?.slice(0, 10) ?? '',
  start_date: props.debt?.start_date?.slice(0, 10) ?? '',
  payment_date: props.debt?.payment_date?.slice(0, 10) ?? '',
  end_date: props.debt?.end_date?.slice(0, 10) ?? '',
  final_amount: props.debt?.final_amount ?? '',
  status: props.debt?.status ?? 'active',
  notes: props.debt?.notes ?? '',
  account_id: props.debt?.account_id ?? '',
  category_id: props.debt?.category_id ?? '',
});

// Заполняем форму при редактировании
onMounted(() => {
  if (isEditing.value) {
    Object.assign(form, {
      _method: 'PUT',
      type: props.debt.type,
      lender_or_borrower: props.debt.lender_or_borrower,
      initial_amount: props.debt.initial_amount,
      remaining_amount: props.debt.remaining_amount,
      currency: props.debt.currency,
      interest_rate: props.debt.interest_rate,
      interest_type: props.debt.interest_type,
      due_date: props.debt.due_date?.slice(0, 10) ?? null,
      status: props.debt.status,
      notes: props.debt.notes || ''
    });
  } else {
    form.remaining_amount = form.initial_amount;
  }
});

// Авто-обновление remaining_amount при создании
watch(() => form.initial_amount, (newVal) => {
  if (!isEditing.value) {
    form.remaining_amount = newVal;
  }
});

// Отправка формы
const submit = () => {
  // Гарантируем, что поле не пустое
  if (!form.remaining_amount) {
    form.remaining_amount = form.initial_amount || 0;
  }
  const params = isEditing.value ? { debt: props.debt.id } : {};
  const opts = { preserveScroll: true };

  if (isEditing.value) {
    form.put(route(submitRoute, params), opts);
  } else {
    form
      .transform(data => ({ ...data, _method: undefined }))
      .post(route(submitRoute, params), opts);
  }
};
</script>

<style scoped>
.form-select {
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5rem;
  box-shadow: var(--tw-ring-offset-shadow,0 0 #0000), var(--tw-ring-shadow,0 0 #0000), var(--tw-shadow);
}
.dark .form-select {
  border-color: #4b5563;
  background-color: #374151;
  color: #d1d5db;
}
</style>
