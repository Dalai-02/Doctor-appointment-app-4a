<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.feedbacks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.feedbacks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'tipo' => 'required|string|in:Queja,Sugerencia,Felicitación',
            'comentario' => 'required|string',
        ]);

        $data['estado'] = 'Pendiente';

        Feedback::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Sugerencia enviada!',
            'text' => 'Tu sugerencia ha sido registrada exitosamente.',
        ]);

        return redirect()->route('admin.feedbacks.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        return view('admin.feedbacks.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
    {
        return view('admin.feedbacks.edit', compact('feedback'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'tipo' => 'required|string|in:Queja,Sugerencia,Felicitación',
            'comentario' => 'required|string',
            'estado' => 'required|string|in:Pendiente,Revisado,Resuelto',
        ]);

        $feedback->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Sugerencia actualizada!',
            'text' => 'La sugerencia ha sido actualizada exitosamente.',
        ]);

        return redirect()->route('admin.feedbacks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Sugerencia eliminada!',
            'text' => 'La sugerencia ha sido eliminada correctamente.',
        ]);

        return redirect()->route('admin.feedbacks.index');
    }
}
