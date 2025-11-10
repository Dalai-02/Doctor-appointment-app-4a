<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validar que se cree correctamente
        $request->validate(['name'=> 'required|unique:roles,name']);

        //Si pasa la validación, crear el rol
        Role::create(['name' => $request->name]);

        //Variable de un sólo uso para alerta
        session()->flash('swal',
        [
            'icon' => 'succes',
            'title' => 'Rol creado correctamente',
            'text' => 'El rol ha sido creado exitosamente'
        ]);

        //Redicciona a la página principal si se creó el rol
        return redirect()->route('admin.roles.index')
        ->with('succes', 'Role created succesully');
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
    public function edit(Role $role)
    {
        //Restringir la accion para los primeros 4 roles fijos
        if($role->id <= 4){
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No puedes editar este rol'
            ]);
            return redirect()->route('admin.roles.index');
        }

        return view('admin.roles.edit', compact('role'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //Validar que se se inserte bien
        $request->validate(['name'=> 'required|unique:roles,name,' . $role->id]);

        //Si el campo no cambio no se tomará en cuenta la validación
        if($role->name === $request->name){
            session()->flash('info',
            [
                'icon' => 'info',
                'title' => 'Sin cambios',
                'text' => 'No se detectaron modificaciones'
            ]);

            //Redireccion al mismo lugar
            return redirect()->route('admin.roles.edit', $role);
        }

        //Si pasa la validación, ediatará el rol
        $role->update(['name'=> $request->name]);

        //Variable de un sólo uso para alerta
        session()->flash('swal',
        [
            'icon' => 'succes',
            'title' => 'Rol actualizado correctamente',
            'text' => 'El rol ha sido aztualizado exitosamente'
        ]);

        //Redicciona a la página principal si se creó el rol
        return redirect()->route('admin.roles.index', $role);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Role $role)
    {
        //Restringir la accion para los primeros 4 roles fijos
        if($role->id <=4){
            //Variable de un sólo uso para alerta
            session()->flash('swal',
            [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No puedes eliminar este rol'
            ]);

            return redirect()->route('admin.roles.index');
        }

        //Borrar el elemnto
        $role->delete();

        //Alerta
        session()->flash('swal',
        [
            'icon' => 'success',
            'title' => 'Rol eliminado correctamente',
            'text' => 'El rol ha sido eliminado exitosamente'
        ]);

        return redirect()->route('admin.roles.index');
    }
}
