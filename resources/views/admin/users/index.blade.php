<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Управление пользователями и их ролями') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 text-green-500 font-medium">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h1 class="text-lg font-bold mb-4">Пользователи</h1>
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Имя</th>
                                <th class="border border-gray-300 px-4 py-2">Email</th>
                                <th class="border border-gray-300 px-4 py-2">Роли</th>
                                <th class="border border-gray-300 px-4 py-2">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $user->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $user->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $user->email }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        @foreach($user->roles as $role)
                                            <span class="inline-block bg-blue-500 text-white px-2 py-1 rounded text-sm mr-2">
                                                {{ $role->name }}
                                                <form action="{{ route('admin.users.removeRole', [$user->id, $role->name]) }}" method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning">удалить роль: {{ $role->name }}</button>
                                                </form>
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <form action="{{ route('admin.users.assignRole', $user->id) }}" method="POST" class="flex items-center">
                                            @csrf
                                            <select name="role" class="border border-gray-300 rounded px-2 py-1 text-sm mr-2">
                                                <option value="" disabled selected>Выберите роль</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-primary text-white bg-green-500 px-4 py-2 rounded">
                                                Назначить
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center border border-gray-300 px-4 py-2">Нет пользователей.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
