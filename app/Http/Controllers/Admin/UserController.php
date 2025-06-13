<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User\User;
use App\Models\User\UserType;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $userTypes = UserType::all();
        return view('admin.users.create', compact('userTypes'));
    }
    public function do_create(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'type_id' => ['required', 'integer'],
        ]);
        
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type_id' => $validated['type_id'] ?? null,
            'active' => 1,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente');
    }

    public function edit($id)
    {
        $userTypes = UserType::all();
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user', 'userTypes'));
    }

    public function do_edit(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'type_id' => ['nullable', 'integer'],
            'active' => ['nullable', 'integer'],
        ]);

        $user->name    = $validated['name'];
        $user->email   = $validated['email'];
        $user->type_id = $validated['type_id'] ?? $user->type_id;
        $user->active = $validated['active'] ?? 0;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Usuario editado exitosamente');
    }

    public function do_active($id)
    {
        $user = User::findOrFail($id);

        $user->active = $user->active ? 0 : 1;
        $user->save();

        $status = $user->active ? 'Activado' : 'Desactivado';

        return redirect()->back()->with('success', "Usuario {$status} correctamente.");
    }

    /*     
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()
                ->back()
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente');
    } 
    */
}
