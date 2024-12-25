@section('content')
<div class="container">
    <h1>Список ролей</h1>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mb-3">Добавить роль</a>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Привилегии</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->permissions->pluck('name')->join(', ') }}</td>
                <td>
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">Редактировать</a>
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
