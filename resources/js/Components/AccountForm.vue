<template>
    <form @submit.prevent="submit">
        <div class="space-y-4">
            <div>
                <InputLabel for="name" value="Название счета" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Напр., Карта Visa Сбербанк"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="type" value="Тип счета" />
                <select
                    id="type"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.type"
                    required
                >
                    <option value="card">Карта</option>
                    <option value="bank_account">Банковский счет</option>
                    <option value="cash">Наличные</option>
                    <option value="savings">Вклад / Накопления</option>
                    <option value="other">Другое</option>
                </select>
                <InputError class="mt-2" :message="form.errors.type" />
            </div>

            <div>
                <InputLabel for="currency" value="Валюта" />
                <TextInput
                    id="currency"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.currency"
                    required
                    placeholder="RUB"
                    maxlength="3"
                />
                <InputError class="mt-2" :message="form.errors.currency" />
            </div>

             <div>
                <InputLabel for="balance" value="Начальный баланс" />
                 <TextInput
                    id="balance"
                    type="number"
                    step="0.01"
                    class="mt-1 block w-full"
                    v-model="form.balance"
                    required
                    placeholder="0.00"
                />
                <InputError class="mt-2" :message="form.errors.balance" />
            </div>

            <div>
                <InputLabel for="color" value="Цвет для отображения (необязательно)" />
                <input
                    id="color"
                    type="color"
                    class="mt-1 block w-full h-10 p-1 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                    v-model="form.color"
                />
                <InputError class="mt-2" :message="form.errors.color" />
            </div>


            <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                 <Link :href="cancelRoute" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                    Отмена
                 </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ submitButtonText }}
                </PrimaryButton>
            </div>
        </div>
    </form>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { onMounted } from 'vue';

const props = defineProps({
    account: { // Передается для формы редактирования
        type: Object,
        default: null,
    },
    submitRoute: { // Маршрут для отправки (create или update)
        type: String,
        required: true,
    },
    cancelRoute: { // Маршрут для отмены
        type: String,
        required: true,
    },
     submitButtonText: {
        type: String,
        default: 'Сохранить'
     }
});

// Инициализация формы
const form = useForm({
    name: '',
    type: 'card', // Значение по умолчанию
    currency: 'RUB',
    balance: '0.00',
    color: '#cccccc', // Цвет по умолчанию
    _method: 'POST', // По умолчанию для создания
});

// Если передан account (режим редактирования), заполняем форму
onMounted(() => {
    if (props.account) {
        form.name = props.account.name;
        form.type = props.account.type;
        form.currency = props.account.currency;
        form.balance = props.account.balance; // Баланс обычно не редактируют напрямую, а через транзакции, но для примера оставим
        form.color = props.account.color || '#cccccc';
        form._method = 'PUT'; // Меняем метод для update
    }
});


const submit = () => {
    const routeName = props.submitRoute;
    const routeParams = props.account ? { account: props.account.id } : {};

    if (form._method === 'PUT') {
         form.put(route(routeName, routeParams), {
            preserveScroll: true,
            // onError: (errors) => { /* обработка ошибок */ },
            // onSuccess: () => { /* действия при успехе */ }
        });
    } else {
        form.post(route(routeName, routeParams), {
             preserveScroll: true,
            // onError: (errors) => { /* обработка ошибок */ },
            // onSuccess: () => { /* действия при успехе */ }
        });
    }
};

</script>
