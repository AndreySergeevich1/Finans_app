<script setup>
// Импортируем ref И computed из vue
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

// Иконки для нижней навигации (Heroicons)
import { HomeIcon, ArrowsRightLeftIcon, ChartBarIcon, UserCircleIcon, PlusCircleIcon } from '@heroicons/vue/24/solid';
import { HomeIcon as HomeOutlineIcon, ArrowsRightLeftIcon as ArrowsOutlineIcon, ChartBarIcon as ChartBarOutlineIcon, UserCircleIcon as UserOutlineIcon } from '@heroicons/vue/24/outline';


const showingNavigationDropdown = ref(false);

const page = usePage();
// Теперь 'computed' будет определен и эта строка не вызовет ошибку
const user = computed(() => page.props.auth.user);

const showAddMenu = ref(false);
function toggleAddMenu() {
    showAddMenu.value = !showAddMenu.value;
}
function goTo(routeName) {
    showAddMenu.value = false;
    window.location.href = route(routeName);
}

// Определяем маршруты для пунктов меню
const menuItems = [
    { routeName: 'dashboard', label: 'Главная', icon: HomeOutlineIcon, activeIcon: HomeIcon },
    { routeName: 'transactions.index', label: 'Транзакции', icon: ArrowsOutlineIcon, activeIcon: ArrowsRightLeftIcon },
    { routeName: 'transactions.create', label: 'Добавить', icon: PlusCircleIcon, activeIcon: PlusCircleIcon, isCentral: true },
    { routeName: 'debts.index', label: 'долги', icon: ChartBarOutlineIcon, activeIcon: ChartBarIcon }, // Закомментировано, т.к. маршрута пока нет
    { routeName: 'profile.edit', label: 'Профиль', icon: UserOutlineIcon, activeIcon: UserCircleIcon },
];

</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 pb-16">
            <header class="bg-white dark:bg-gray-800 shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <div v-if="$page.props.flash && $page.props.flash.success" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                 <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                    </div>
                </div>
                 <div v-if="($page.props.flash && $page.props.flash.error) || Object.keys($page.props.errors).length > 0" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                 <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Ошибка!</strong>
                         <span v-if="$page.props.flash && $page.props.flash.error" class="block sm:inline ml-2">{{ $page.props.flash.error }}</span>
                         <ul v-if="Object.keys($page.props.errors).length > 0" class="list-disc ml-6 mt-1">
                             <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
                         </ul>
                    </div>
                </div>

                <slot />
            </main>
            <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg">
                 <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                    <div class="flex justify-around items-center h-16">
                        <template v-for="item in menuItems" :key="item.routeName">
                            <div v-if="item.isCentral" class="relative flex flex-col items-center justify-center w-16 h-16 -mt-6">
                                <button @click="toggleAddMenu" class="flex flex-col items-center justify-center w-16 h-16 bg-indigo-600 rounded-full shadow-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800" aria-label="Добавить">
                                    <component :is="item.icon" class="w-7 h-7" />
                                </button>
                                <div v-if="showAddMenu" class="absolute bottom-20 left-1/2 -translate-x-1/2 bg-white dark:bg-gray-800 rounded-xl shadow-lg py-2 px-4 min-w-[180px] border border-gray-200 dark:border-gray-700 z-50">
                                    <button @click="goTo('debts.create')" class="block w-full text-left px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-700 rounded">Добавить долг</button>
                                    <button @click="goTo('transactions.create')" class="block w-full text-left px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-700 rounded">Добавить транзакцию</button>
                                    <button @click="goTo('mandatory-payments.create')" class="block w-full text-left px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-700 rounded">Добавить обязательный платеж</button>
                                    <button @click="goTo('accounts.create')" class="block w-full text-left px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-700 rounded">Добавить карту/счет</button>
                                </div>
                            </div>
                            <Link
                                v-else
                                :href="item.routeName ? route(item.routeName) : '#'"
                                class="flex flex-col items-center justify-center px-2 pt-1 text-center w-16"
                                :class="[
                                    route().current(item.routeName + '*')
                                    ? 'text-indigo-600 dark:text-indigo-400'
                                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                                ]"
                            >
                                <component
                                    :is="route().current(item.routeName + '*') ? item.activeIcon : item.icon"
                                    class="w-6 h-6 mb-1"
                                />
                                <span class="text-xs truncate">{{ item.label }}</span>
                            </Link>
                        </template>
                    </div>
                 </div>
            </nav>
             </div>
    </div>
</template>
