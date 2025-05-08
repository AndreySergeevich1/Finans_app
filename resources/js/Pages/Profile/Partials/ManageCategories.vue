<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
  categories: {
    type: Array,
    required: true
  },
  currencies: {
    type: Array,
    required: true
  }
});

const categoryForm = useForm({
  name: '',
  type: 'expense',
  color: '#3B82F6',
  parent_id: null,
  icon: ''
});

const currencyForm = useForm({
  code: '',
  name: '',
  symbol: ''
});

const editingCategory = ref(null);
const editingCurrency = ref(null);

const colorOptions = [
  { value: '#3B82F6', label: 'Синий' },
  { value: '#10B981', label: 'Зеленый' },
  { value: '#EF4444', label: 'Красный' },
  { value: '#F59E0B', label: 'Желтый' },
  { value: '#8B5CF6', label: 'Фиолетовый' },
  { value: '#EC4899', label: 'Розовый' },
  { value: '#6366F1', label: 'Индиго' },
  { value: '#6B7280', label: 'Серый' }
];

const typeOptions = [
  { value: 'expense', label: 'Расход' },
  { value: 'income', label: 'Доход' }
];

const editCategory = (category) => {
  editingCategory.value = category;
  categoryForm.name = category.name;
  categoryForm.type = category.type;
  categoryForm.color = category.color;
};

const editCurrency = (currency) => {
  editingCurrency.value = currency;
  currencyForm.code = currency.code;
  currencyForm.name = currency.name;
  currencyForm.symbol = currency.symbol;
};

const submitCategory = () => {
  if (editingCategory.value) {
    categoryForm.put(route('categories.update', editingCategory.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        categoryForm.reset();
        editingCategory.value = null;
      }
    });
  } else {
    categoryForm.post(route('categories.store'), {
      preserveScroll: true,
      onSuccess: () => categoryForm.reset()
    });
  }
};

const submitCurrency = () => {
  if (editingCurrency.value) {
    currencyForm.put(route('currencies.update', editingCurrency.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        currencyForm.reset();
        editingCurrency.value = null;
      }
    });
  } else {
    currencyForm.post(route('currencies.store'), {
      preserveScroll: true,
      onSuccess: () => currencyForm.reset()
    });
  }
};

const deleteCategory = (category) => {
  if (confirm('Вы уверены, что хотите удалить эту категорию?')) {
    categoryForm.delete(route('categories.destroy', category.id), {
      preserveScroll: true
    });
  }
};

const deleteCurrency = (currency) => {
  if (confirm('Вы уверены, что хотите удалить эту валюту?')) {
    currencyForm.delete(route('currencies.destroy', currency.id), {
      preserveScroll: true
    });
  }
};
</script>

<template>
  <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <div class="max-w-xl">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Управление категориями и валютами</h2>


      <div class="mt-6">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">
          {{ editingCategory ? 'Редактировать категорию' : 'Добавить категорию' }}
        </h3>
        <form @submit.prevent="submitCategory" class="space-y-4">
          <div>
            <InputLabel for="category_name" value="Название категории" />
            <TextInput
              id="category_name"
              type="text"
              class="mt-1 block w-full"
              v-model="categoryForm.name"
              required
              autofocus
              placeholder="Напр., Продукты"
            />
            <InputError :message="categoryForm.errors.name" class="mt-2" />
          </div>

          <div>
            <InputLabel value="Тип" />
            <div class="mt-1 flex rounded-md shadow-sm">
              <button
                type="button"
                @click="categoryForm.type = 'expense'"
                :class="[
                  'relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                  categoryForm.type === 'expense'
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : 'bg-white text-gray-700 hover:bg-gray-50'
                ]"
              >
                Расход
              </button>
              <button
                type="button"
                @click="categoryForm.type = 'income'"
                :class="[
                  '-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                  categoryForm.type === 'income'
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : 'bg-white text-gray-700 hover:bg-gray-50'
                ]"
              >
                Доход
              </button>
            </div>
            <InputError :message="categoryForm.errors.type" class="mt-2" />
          </div>

          <div>
            <InputLabel for="category_color" value="Цвет категории" />
            <div class="flex items-center space-x-2">
              <input
                type="color"
                id="category_color"
                class="h-10 w-10 p-1 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                v-model="categoryForm.color"
                required
              />
              <SelectInput
                class="mt-1 block w-full"
                v-model="categoryForm.color"
                required
              >
                <option v-for="option in colorOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </SelectInput>
            </div>
            <InputError :message="categoryForm.errors.color" class="mt-2" />
          </div>

          <div>
            <InputLabel for="category_parent" value="Родительская категория (необязательно)" />
            <select
              id="category_parent"
              v-model="categoryForm.parent_id"
              class="mt-1 block w-full form-select"
            >
              <option :value="null">-- Нет (корневая категория) --</option>
              <option
                v-for="category in categories.filter(c => c.id !== editingCategory?.id)"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <InputError :message="categoryForm.errors.parent_id" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">
              Позволяет создавать иерархию (напр., Еда -> Продукты).
            </p>
          </div>

          <div>
            <InputLabel for="category_icon" value="Название иконки (необязательно)" />
            <TextInput
              id="category_icon"
              type="text"
              class="mt-1 block w-full"
              v-model="categoryForm.icon"
              placeholder="shopping-cart (напр., из Heroicons)"
            />
            <InputError :message="categoryForm.errors.icon" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">
              Используйте названия иконок из выбранной библиотеки (напр., Heroicons).
            </p>
          </div>

          <div class="flex items-center justify-end pt-6 mt-6 border-t border-gray-200 dark:border-gray-700 space-x-3">
            <button
              type="button"
              @click="editingCategory = null; categoryForm.reset()"
              class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
            >
              Отмена
            </button>
            <PrimaryButton :class="{ 'opacity-25': categoryForm.processing }" :disabled="categoryForm.processing">
              {{ editingCategory ? 'Сохранить' : 'Добавить категорию' }}
            </PrimaryButton>
          </div>
        </form>
      </div>


      <div class="mt-8">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Добавить валюту</h3>
        <form @submit.prevent="submitCurrency" class="space-y-4">
          <div>
            <InputLabel for="currency_code" value="Код валюты" />
            <TextInput
              id="currency_code"
              type="text"
              class="mt-1 block w-full"
              v-model="currencyForm.code"
              required
              maxlength="3"
            />
            <InputError :message="currencyForm.errors.code" class="mt-2" />
          </div>

          <div>
            <InputLabel for="currency_name" value="Название валюты" />
            <TextInput
              id="currency_name"
              type="text"
              class="mt-1 block w-full"
              v-model="currencyForm.name"
              required
            />
            <InputError :message="currencyForm.errors.name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="currency_symbol" value="Символ валюты" />
            <TextInput
              id="currency_symbol"
              type="text"
              class="mt-1 block w-full"
              v-model="currencyForm.symbol"
              required
              maxlength="5"
            />
            <InputError :message="currencyForm.errors.symbol" class="mt-2" />
          </div>

          <div class="flex items-center justify-end">
            <PrimaryButton :class="{ 'opacity-25': currencyForm.processing }" :disabled="currencyForm.processing">
              Добавить валюту
            </PrimaryButton>
          </div>
        </form>
      </div>


      <div class="mt-8">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Существующие категории</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="category in categories" :key="category.id" class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-4 h-4 rounded-full" :style="{ backgroundColor: category.color || '#6B7280' }"></div>
              <div>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ category.name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ category.type === 'expense' ? 'Расход' : 'Доход' }}
                </p>
              </div>
            </div>
            <div class="flex space-x-2">
              <button @click="editCategory(category)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button @click="deleteCategory(category)" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>


      <div class="mt-8">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Существующие валюты</h3>
        <div class="space-y-2">
          <div v-for="currency in currencies" :key="currency.code"
               class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="flex items-center space-x-3">
              <span class="font-medium">{{ currency.code }}</span>
              <span class="text-gray-500 dark:text-gray-400">({{ currency.symbol }})</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ currency.name }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <button @click="editCurrency(currency)"
                      class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button @click="deleteCurrency(currency)"
                      class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
