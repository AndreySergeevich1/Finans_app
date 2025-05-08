<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Профиль') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-6">Меню профиля</h3>
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('profile.edit') }}" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">🔒 Безопасность (имя, e-mail, пароль)</a>
                    <a href="{{ route('shifts.index') }}" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">📅 График смен</a>
                    <a href="{{ route('categories.index') }}" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">🏷️ Редактирование категорий</a>
                    <a href="{{ route('currencies.index') }}" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">💱 Редактирование валют</a>
                    <a href="{{ route('profile.rate') }}" class="block p-4 rounded bg-gray-100 hover:bg-blue-100 transition">💸 Редактирование ставки за смену и премии</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full p-4 rounded bg-red-100 hover:bg-red-200 text-red-700 font-semibold transition">🚪 Выйти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 