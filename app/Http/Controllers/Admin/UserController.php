<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('q')) {
            $query->where('name', 'ilike', '%'.$request->q.'%')->orWhere('email', 'ilike', '%'.$request->q.'%');
        }
        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,trabajador,cliente',
        ]);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return back()->with('success', 'Usuario creado.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,trabajador,cliente',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');

        $user->update($data);

        return back()->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'Usuario eliminado.');
    }
}
