<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Управление Байерами') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Список пользователей с ролью "buyer" -->
                    <h1 class="text-lg font-bold mb-4">Пользователи с ролью "Байер"</h1>
                    @if(session('success'))
                        <div class="mb-4 text-green-500 font-medium">
                            {{ session('success') }}
                        </div>
                    @endif
                    <table class="table-auto w-full border-collapse border border-gray-300 mb-6">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Имя</th>
                                <th class="border border-gray-300 px-4 py-2">Email</th>
                                <th class="border border-gray-300 px-4 py-2">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buyers as $buyer)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $buyer->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $buyer->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $buyer->email }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <form action="{{ route('teamlead.buyers.remove', $buyer->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">
                                                Удалить роль
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center border border-gray-300 px-4 py-2">Нет пользователей с ролью "Байер".</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Список пользователей без роли "buyer" -->
                    <h1 class="text-lg font-bold mb-4">Пользователи без роли "Байер"</h1>
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Имя</th>
                                <th class="border border-gray-300 px-4 py-2">Email</th>
                                <th class="border border-gray-300 px-4 py-2">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usersWithoutBuyerRole as $user)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $user->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $user->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $user->email }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <form action="{{ route('teamlead.buyers.assign', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                Назначить роль
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center border border-gray-300 px-4 py-2">Нет доступных пользователей для назначения роли "Байер".</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
