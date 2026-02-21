<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Speciality;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('user', 'speciality')->get();

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $specialities = Speciality::all();

        $users = User::role('Médico')
            ->doesntHave('doctor')
            ->get();

        return view('admin.doctors.create', compact('specialities', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'speciality_id' => 'required|exists:specialities,id',
            'license_number' => 'required|digits_between:6,8',
            'biography' => 'nullable|string|max:255',
        ], [
            'license_number.required' => 'El número de licencia es obligatorio.',
            'license_number.digits_between' => 'La licencia solo puede contener 6 a 8 dígitos.',
            'biography.max' => 'La biografía no puede exceder los 255 caracteres.',
        ]);

        Doctor::create([
            'user_id' => $request->user_id,
            'speciality_id' => $request->speciality_id,
            'license_number' => $request->license_number,
            'biography' => $request->biography,
        ]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Doctor registrado',
            'text' => 'El doctor ha sido registrado correctamente.',
        ]);

        return redirect()->route('admin.doctors.index');
    }

    public function edit(Doctor $doctor)
    {
        $specialities = Speciality::all();

        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'speciality_id' => 'required|exists:specialities,id',
            'license_number' => 'required|digits_between:6,8',
            'biography' => 'nullable|string|max:255',
        ], [
            'license_number.required' => 'El número de licencia es obligatorio.',
            'license_number.digits_between' => 'La licencia solo puede contener 6 a 8 dígitos.',
            'biography.max' => 'La biografía no puede exceder los 255 caracteres.',
        ]);

        $doctor->update([
            'speciality_id' => $request->speciality_id,
            'license_number' => $request->license_number,
            'biography' => $request->biography,
        ]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Doctor actualizado',
            'text' => 'El doctor ha sido actualizado correctamente.',
        ]);

        return redirect()->route('admin.doctors.index');
    }

    public function destroy(Doctor $doctor)
    {
        // Obtener el usuario asociado
        $user = $doctor->user;
        
        // Eliminar el doctor
        $doctor->delete();
        
        // Quitar todos los roles del usuario
        $user->roles()->detach();
        
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Doctor eliminado',
            'text' => 'El doctor ha sido eliminado correctamente.',
        ]);

        return redirect()->route('admin.doctors.index');
    }
}