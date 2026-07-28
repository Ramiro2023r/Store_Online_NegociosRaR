@extends('layouts.admin')
@section('title', 'Usuarios - Admin')
@section('page-title', '👥 Gestión de Usuarios y Trabajadores')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white border rounded-xl p-6 h-fit">
        <h3 class="font-bold mb-4">Nuevo usuario</h3>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="text-sm font-medium">Nombre</label>
                <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Correo</label>
                <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Contraseña</label>
                <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Rol</label>
                <select name="role" class="w-full border rounded-lg px-3 py-2 mt-1">
                    <option value="cliente">Cliente</option>
                    <option value="trabajador">Trabajador</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <button class="w-full bg-rar-600 text-white font-semibold py-2 rounded-lg text-sm hover:bg-rar-700">Crear usuario</button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white border rounded-xl overflow-hidden h-fit">
        <div class="p-3 border-b">
            <form method="GET"><input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar usuario..." class="border rounded-lg px-3 py-2 text-sm w-64"></form>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="px-4 py-3">Nombre</th><th class="px-4 py-3">Correo</th><th class="px-4 py-3">Rol</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y">
                @foreach($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="flex items-center gap-2">
                                @csrf @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <select name="role" onchange="this.form.submit()" class="border rounded-lg px-2 py-1 text-xs">
                                    <option value="cliente" {{ $user->role=='cliente'?'selected':'' }}>Cliente</option>
                                    <option value="trabajador" {{ $user->role=='trabajador'?'selected':'' }}>Trabajador</option>
                                    <option value="admin" {{ $user->role=='admin'?'selected':'' }}>Admin</option>
                                </select>
                                <input type="hidden" name="active" value="{{ $user->active ? 1 : 0 }}">
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $user->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $user->active ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
