<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Редактировать роль: ') . $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-lg font-bold mb-4">Редактировать роль</h1>

                    <!-- Кнопка "Вернуться обратно" -->
                    <a href="{{ route('admin.roles.index') }}"
                        class="mb-4 inline-block bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
                        Вернуться обратно
                    </a>

                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Название роли</label>
                            <input type="text" name="name" id="name" value="{{ $role->name }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="permissions" class="block text-sm font-medium text-gray-700">Привилегии</label>
                            <select name="permissions[]" id="permissions"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                multiple>
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->name }}"
                                        {{ $role->hasPermissionTo($permission->name) ? 'selected' : '' }}>
                                        {{ $permission->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Кнопка для сохранения -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="btn btn-primary">
                                Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
