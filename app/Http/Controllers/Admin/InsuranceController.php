<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.insurances.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.insurances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'telefono_contacto' => 'required|string|max:20',
            'notas_adicionales' => 'nullable|string',
        ]);

        Insurance::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Aseguradora registrada!',
            'text' => 'La aseguradora ha sido registrada exitosamente.',
        ]);

        return redirect()->route('admin.insurances.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Insurance $insurance)
    {
        return view('admin.insurances.show', compact('insurance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insurance $insurance)
    {
        return view('admin.insurances.edit', compact('insurance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Insurance $insurance)
    {
        $data = $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'telefono_contacto' => 'required|string|max:20',
            'notas_adicionales' => 'nullable|string',
        ]);

        $insurance->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Aseguradora actualizada!',
            'text' => 'La aseguradora ha sido actualizada exitosamente.',
        ]);

        return redirect()->route('admin.insurances.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insurance $insurance)
    {
        $insurance->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Aseguradora eliminada!',
            'text' => 'La aseguradora ha sido eliminada correctamente.',
        ]);

        return redirect()->route('admin.insurances.index');
    }
}
