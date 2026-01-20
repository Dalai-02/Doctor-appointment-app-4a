<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
     {
        return view('admin.users.index', [
            'title' => 'Usuarios',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Usuarios']
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|max:20|unique:users',
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:5|max:255',
            'role_id' => 'required|exists:roles,id',

        ]);

        $user = User::create($data);

        $user->roles()->attach($data['role_id']);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado',
            'text' => 'El usuario ha sido creado exitosamente.',
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'id_number' => 'required|string|max:20|unique:users,id_number,' . $user->id,
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:5|max:255',
            'role_id' => 'required|exists:roles,id',

        ]);

        $user->update($data);

        //Si el usuario quiere editar su contraseña, que lo guarde 
        if ($request->filled('password')) {
            $user->password = bycrypt($request->input('password'));
            $user->save();
        }

        $user->roles()->sync($data['role_id']);
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario actualizado',
            'text' => 'El usuario ha sido actualizado exitosamente.',
        ]);
        return redirect()->route('admin.users.edit', $user)->with('success', 'Usuario actualizado exitosamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
