<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportPatientsFromFileJob;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\BloodType;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.patients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'patients_file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ]);

        $file = $request->file('patients_file');
        $hash = md5_file($file->path());

        if (\Illuminate\Support\Facades\Cache::has('imported_file_' . $hash)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'patients_file' => 'Este archivo ya fue procesado previamente. No se permiten datos duplicados.'
            ]);
        }

        \Illuminate\Support\Facades\Cache::put('imported_file_' . $hash, true, now()->addDays(30));

        $storedPath = Storage::disk('local')->putFile('imports/patients', $file);

        ImportPatientsFromFileJob::dispatch($storedPath, auth()->id())->onQueue('imports');

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Importación en proceso',
            'text' => 'El archivo fue recibido y se está procesando en segundo plano.',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Importación en proceso']);
        }

        return redirect()->route('admin.patients.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function importProgress()
    {
        $progress = \Illuminate\Support\Facades\Cache::get('import_progress_' . auth()->id());
        
        if (!$progress) {
            return response()->json(['total' => 0, 'processed' => 0]);
        }

        return response()->json($progress);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $bloodTypes = BloodType::all();
        return view('admin.patients.edit', compact('patient', 'bloodTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|min:3|max:50',
            'chronic_conditions' => 'nullable|string|min:3|max:50',
            'surgical_history' => 'nullable|string|min:3|max:50',
            'family_history' => 'nullable|string|min:3|max:50',
            'observations' => 'nullable|string|min:3|max:50',
            'emergency_contact_name' => 'nullable|string|min:3|max:255',
            'emergency_contact_phone' => 'nullable|string|min:10|max:12',
            'emergency_contact_relationship' => 'nullable|string|max:50',
        ]);
        $patient->update($data);
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Paciente actualizado!',
            'text' => 'Los datos del paciente han sido actualizados exitosamente.',
        ]);
        return redirect()->route('admin.patients.edit', $patient)->with('success', 'Paciente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Obtener el usuario asociado
        $user = $patient->user;
        
        // Eliminar el paciente
        $patient->delete();
        
        // Quitar todos los roles del usuario
        $user->roles()->detach();
        
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Paciente eliminado',
            'text' => 'El paciente ha sido eliminado correctamente.',
        ]);
        
        return redirect()->route('admin.patients.index');
    }
}
