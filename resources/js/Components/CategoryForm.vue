<script setup>
import { onMounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    category: { type: Object, default: null },
    potentialParents: { type: Array, default: () => [] }, // Список для выбора родителя
    submitRoute: { type: String, required: true },
    cancelRoute: { type: String, required: true },
    submitButtonText: { type: String, default: 'Сохранить' }
});

const form = useForm({
    _method: 'POST',
    name: '',
    type: 'expense', // По умолчанию расход
    parent_id: null,
    icon: '',
});

onMounted(() => {
    if (props.category) {
        form._method = 'PUT';
        form.name = props.category.name;
        form.type = props.category.type;
        form.parent_id = props.category.parent_id;
        form.icon = props.category.icon || '';
    }
});

const submit = () => {
    const routeParams = props.category ? { category: props.category.id } : {};
    const options = { preserveScroll: true };

    if (form._method === 'PUT') {
        form.put(route(props.submitRoute, routeParams), options);
    } else {
        form.transform(data => ({ ...data, _method: undefined }))
          .post(route(props.submitRoute, routeParams), options);
    }
};
</script>

<template>
     <form @submit.prevent="submit">
        <div class="space-y-4">
            <div>
                <InputLabel for="name" value="Название категории" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus placeholder="Напр., Продукты" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

             <div>
                <InputLabel value="Тип" />
                 <div class="mt-1 flex rounded-md shadow-sm">
                    <button type="button" @click="form.type = 'expense'" :class="['relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500', form.type === 'expense' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50']">Расход</button>
                    <button type="button" @click="form.type = 'income'" :class="['-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500', form.type === 'income' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50']">Доход</button>
                </div>
                 <InputError class="mt-2" :message="form.errors.type" />
            </div>

            <div>
                <InputLabel for="parent_id" value="Родительская категория (необязательно)" />
                <select id="parent_id" v-model="form.parent_id" class="mt-1 block w-full form-select">
                    <option :value="null">-- Нет (корневая категория) --</option>
                    <option v-for="parent in potentialParents" :key="parent.id" :value="parent.id">
                        {{ parent.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.parent_id" />
                 <p class="text-xs text-gray-500 mt-1">Позволяет создавать иерархию (напр., Еда -> Продукты).</p>
            </div>

            <div>
                 <InputLabel for="icon" value="Название иконки (необязательно)" />
                 <TextInput id="icon" type="text" class="mt-1 block w-full" v-model="form.icon" placeholder="shopping-cart (напр., из Heroicons)" />
                 <InputError class="mt-2" :message="form.errors.icon" />
                 <p class="text-xs text-gray-500 mt-1">Используйте названия иконок из выбранной библиотеки (напр., Heroicons).</p>
            </div>

        </div>
         <div class="flex items-center justify-end pt-6 mt-6 border-t border-gray-200 dark:border-gray-700 space-x-3">
             <Link :href="route(cancelRoute)">
                 <SecondaryButton type="button">Отмена</SecondaryButton>
             </Link>
             <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ submitButtonText }}
            </PrimaryButton>
        </div>
     </form>
</template>


