<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
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
    submitRoute: {
        type: String,
        required: true
    },
    cancelRoute: {
        type: String,
        required: true
    },
    recurringPayment: {
        type: Object,
        default: null
    }
});

const form = useForm({
    account_id: props.recurringPayment?.account_id || '',
    category_id: props.recurringPayment?.category_id || '',
    amount: props.recurringPayment?.amount || '',
    description: props.recurringPayment?.description || '',
    frequency: props.recurringPayment?.frequency || 'monthly',
    start_date: props.recurringPayment?.start_date || '',
    end_date: props.recurringPayment?.end_date || '',
    is_active: props.recurringPayment?.is_active ?? true
});

const frequencies = [
    { value: 'daily', label: 'Ежедневно' },
    { value: 'weekly', label: 'Еженедельно' },
    { value: 'monthly', label: 'Ежемесячно' },
    { value: 'quarterly', label: 'Ежеквартально' },
    { value: 'yearly', label: 'Ежегодно' }
];

const submit = () => {
    if (props.recurringPayment) {
        form.put(route(props.submitRoute, props.recurringPayment.id));
    } else {
        form.post(route(props.submitRoute));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <InputLabel for="account_id" value="Счет" />
            <SelectInput
                id="account_id"
                v-model="form.account_id"
                class="mt-1 block w-full"
                required
            >
                <option value="">Выберите счет</option>
                <option v-for="account in accounts" :key="account.id" :value="account.id">
                    {{ account.name }} ({{ account.currency }})
                </option>
            </SelectInput>
            <InputError :message="form.errors.account_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="category_id" value="Категория" />
            <SelectInput
                id="category_id"
                v-model="form.category_id"
                class="mt-1 block w-full"
                required
            >
                <option value="">Выберите категорию</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </SelectInput>
            <InputError :message="form.errors.category_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="amount" value="Сумма" />
            <TextInput
                id="amount"
                type="number"
                step="0.01"
                v-model="form.amount"
                class="mt-1 block w-full"
                required
            />
            <InputError :message="form.errors.amount" class="mt-2" />
        </div>

        <div>
            <InputLabel for="description" value="Описание" />
            <TextInput
                id="description"
                type="text"
                v-model="form.description"
                class="mt-1 block w-full"
            />
            <InputError :message="form.errors.description" class="mt-2" />
        </div>

        <div>
            <InputLabel for="frequency" value="Периодичность" />
            <SelectInput
                id="frequency"
                v-model="form.frequency"
                class="mt-1 block w-full"
                required
            >
                <option v-for="freq in frequencies" :key="freq.value" :value="freq.value">
                    {{ freq.label }}
                </option>
            </SelectInput>
            <InputError :message="form.errors.frequency" class="mt-2" />
        </div>

        <div>
            <InputLabel for="start_date" value="Дата начала" />
            <TextInput
                id="start_date"
                type="date"
                v-model="form.start_date"
                class="mt-1 block w-full"
                required
            />
            <InputError :message="form.errors.start_date" class="mt-2" />
        </div>

        <div>
            <InputLabel for="end_date" value="Дата окончания (необязательно)" />
            <TextInput
                id="end_date"
                type="date"
                v-model="form.end_date"
                class="mt-1 block w-full"
            />
            <InputError :message="form.errors.end_date" class="mt-2" />
        </div>

        <div class="flex items-center">
            <input
                id="is_active"
                type="checkbox"
                v-model="form.is_active"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            />
            <InputLabel for="is_active" value="Активен" class="ml-2" />
        </div>

        <div class="flex items-center justify-end gap-4">
            <SecondaryButton
                type="button"
                @click="$inertia.visit(route(cancelRoute))"
                :disabled="form.processing"
            >
                Отмена
            </SecondaryButton>

            <PrimaryButton :disabled="form.processing">
                {{ recurringPayment ? 'Обновить' : 'Создать' }}
            </PrimaryButton>
        </div>
    </form>
</template> 