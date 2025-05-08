<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    transaction: { type: Object, default: null },
    accounts: { type: Array, required: true },
    incomeCategories: { type: Array, required: true },
    expenseCategories: { type: Array, required: true },
    prefilledType: { type: String, default: 'expense' },
    submitRoute: { type: String, required: true },
    cancelRoute: { type: String, required: true },
    submitButtonText: { type: String, default: 'Сохранить' }
});

const form = useForm({
    _method: 'POST',
    type: props.prefilledType,
    account_id: props.accounts?.length > 0 ? props.accounts[0].id : null,
    related_account_id: null,
    category_id: null,
    amount: '',
    transaction_date: new Date().toISOString().slice(0, 10),
    description: '',
});

// Добавляем computed свойство для преобразования amount в строку
const amountString = computed({
    get: () => form.amount.toString(),
    set: (value) => {
        form.amount = value === '' ? '' : parseFloat(value);
    }
});

// Фильтруем категории в зависимости от выбранного типа транзакции
const availableCategories = computed(() => {
    if (form.type === 'transfer') return [];
    return form.type === 'income' ? props.incomeCategories : props.expenseCategories;
});

// Фильтруем доступные счета для перевода (исключаем текущий счет)
const availableTransferAccounts = computed(() => {
    if (!props.accounts) return [];
    return props.accounts.filter(account => account.id !== form.account_id);
});

// При изменении типа транзакции
watch(() => form.type, (newType) => {
    if (newType === 'transfer') {
        form.category_id = null;
        form.related_account_id = availableTransferAccounts.value[0]?.id || null;
    } else {
        form.related_account_id = null;
        const currentCategoryExists = availableCategories.value.some(cat => cat.id === form.category_id);
        if (!currentCategoryExists) {
            form.category_id = null;
        }
    }
});

// При изменении счета
watch(() => form.account_id, (newAccountId) => {
    if (form.type === 'transfer' && form.related_account_id === newAccountId) {
        form.related_account_id = availableTransferAccounts.value[0]?.id || null;
    }
});

onMounted(() => {
    if (props.transaction) {
        form._method = 'PUT';
        form.type = props.transaction.type;
        form.account_id = props.transaction.account_id;
        form.related_account_id = props.transaction.related_account_id || null;
        form.category_id = props.transaction.category_id || null;
        form.amount = props.transaction.amount;
        form.transaction_date = props.transaction.transaction_date 
            ? new Date(props.transaction.transaction_date).toISOString().slice(0, 10) 
            : new Date().toISOString().slice(0, 10);
        form.description = props.transaction.description || '';
    }
});

const submit = () => {
    const routeParams = props.transaction ? { transaction: props.transaction.id } : {};
    const options = {
        preserveScroll: true,
        onError: (errors) => { 
            console.error('Validation Errors:', errors);
            // Сбрасываем category_id для переводов
            if (form.type === 'transfer') {
                form.category_id = null;
            }
        }
    };

    if (form._method === 'PUT') {
        form.put(route(props.submitRoute, routeParams), options);
    } else {
        form.post(route(props.submitRoute, routeParams), options);
    }
};

const formatCurrency = (value, currency = 'RUB') => {
    if (value == null) return '';
    try {
        return new Intl.NumberFormat('ru-RU', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(value);
    } catch (e) {
        console.error("Error formatting currency:", e);
        return value;
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="space-y-4">
            <div>
                <InputLabel value="Тип" />
                <div class="mt-1 flex rounded-md shadow-sm">
                    <button 
                        type="button" 
                        @click="form.type = 'expense'" 
                        :class="[
                            'relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                            form.type === 'expense' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        Расход
                    </button>
                    <button 
                        type="button" 
                        @click="form.type = 'income'" 
                        :class="[
                            '-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                            form.type === 'income' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        Доход
                    </button>
                    <button 
                        type="button" 
                        @click="form.type = 'transfer'" 
                        :class="[
                            '-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                            form.type === 'transfer' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        Перевод
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.type" />
            </div>

            <div>
                <InputLabel :value="form.type === 'transfer' ? 'Счет списания' : 'Счет'" />
                <select 
                    id="account_id" 
                    v-model="form.account_id" 
                    required 
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option v-if="!props.accounts?.length" disabled>Нет доступных счетов</option>
                    <option v-for="account in props.accounts || []" :key="account.id" :value="account.id">
                        {{ account.name }} ({{ formatCurrency(account.balance, account.currency) }})
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.account_id" />
            </div>

            <div v-if="form.type === 'transfer'">
                <InputLabel value="Счет зачисления" />
                <select 
                    id="related_account_id" 
                    v-model="form.related_account_id" 
                    required 
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option v-if="!availableTransferAccounts.length" disabled>Нет доступных счетов</option>
                    <option v-for="account in availableTransferAccounts" :key="account.id" :value="account.id">
                        {{ account.name }} ({{ formatCurrency(account.balance, account.currency) }})
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.related_account_id" />
            </div>

            <div v-if="form.type !== 'transfer'">
                <InputLabel for="category_id" value="Категория" />
                <select 
                    id="category_id" 
                    v-model="form.category_id" 
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option :value="null">-- Не выбрана --</option>
                    <option v-if="!availableCategories.length" disabled>
                        Нет категорий для типа "{{ form.type === 'income' ? 'доход' : 'расход' }}"
                    </option>
                    <option v-for="category in availableCategories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.category_id" />
            </div>

            <div>
                <InputLabel for="amount" value="Сумма" />
                <TextInput 
                    id="amount" 
                    type="number" 
                    step="0.01" 
                    class="mt-1 block w-full" 
                    v-model="amountString" 
                    required 
                    placeholder="0.00" 
                />
                <InputError class="mt-2" :message="form.errors.amount" />
            </div>

            <div>
                <InputLabel for="transaction_date" value="Дата" />
                <TextInput 
                    id="transaction_date" 
                    type="date" 
                    class="mt-1 block w-full" 
                    v-model="form.transaction_date" 
                    required 
                />
                <InputError class="mt-2" :message="form.errors.transaction_date" />
            </div>

            <div>
                <InputLabel for="description" value="Описание (необязательно)" />
                <TextInput 
                    id="description" 
                    type="text" 
                    class="mt-1 block w-full" 
                    v-model="form.description" 
                    :placeholder="form.type === 'transfer' ? 'Напр., Перевод на накопления' : 'Напр., Обед в кафе'" 
                />
                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700 space-x-3">
                <Link :href="route(cancelRoute)">
                    <SecondaryButton type="button">Отмена</SecondaryButton>
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ submitButtonText }}
                </PrimaryButton>
            </div>
        </div>
    </form>
</template>
