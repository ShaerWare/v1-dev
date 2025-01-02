<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Роли') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-lg font-bold mb-4">Список ролей</h1>

                    <!-- Кнопка "Создать новую роль" -->
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary" role="button">
                        Создать новую роль
                    </a><br></br>

                    @if(session('success'))
                        <div class="mb-4 text-green-500 font-medium">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">#</th>
                                <th class="border border-gray-300 px-4 py-2">Название</th>
                                <th class="border border-gray-300 px-4 py-2">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $role->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $role->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-primary">Редактировать</a>
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
