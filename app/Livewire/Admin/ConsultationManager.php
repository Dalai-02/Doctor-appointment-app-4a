<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Consultation;
use Livewire\Component;

class ConsultationManager extends Component
{
    public Appointment $appointment;

    public string $diagnosis = '';
    public string $treatment = '';
    public ?string $notes = null;

    public array $medications = [];

    public function mount(Appointment $appointment): void
    {
        $this->appointment = $appointment->load(['patient.user', 'patient.bloodType', 'doctor.user', 'consultation']);

        if ($this->appointment->consultation) {
            $consultation = $this->appointment->consultation;

            $this->diagnosis = $consultation->diagnosis;
            $this->treatment = $consultation->treatment;
            $this->notes = $consultation->notes;
            $this->medications = collect($consultation->medications ?? [])->map(function ($medication) {
                return [
                    'name' => $medication['name'] ?? '',
                    'dose' => $medication['dose'] ?? '',
                    'frequency_duration' => $medication['frequency_duration'] ?? ($medication['instructions'] ?? ''),
                ];
            })->values()->all();
        }

        if (count($this->medications) < 2) {
            while (count($this->medications) < 2) {
                $this->medications[] = [
                    'name' => '',
                    'dose' => '',
                    'frequency_duration' => '',
                ];
            }
        }
    }

    public function addMedication(): void
    {
        $this->medications[] = [
            'name' => '',
            'dose' => '',
            'frequency_duration' => '',
        ];
    }

    public function removeMedication(int $index): void
    {
        if (count($this->medications) <= 2) {
            return;
        }

        unset($this->medications[$index]);
        $this->medications = array_values($this->medications);
    }

    public function saveConsultation(): void
    {
        $validated = $this->validate([
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'medications' => 'required|array|min:2',
            'medications.*.name' => 'required|string',
            'medications.*.dose' => 'required|string',
            'medications.*.frequency_duration' => 'required|string',
        ], [
            'diagnosis.required' => 'El diagnóstico es obligatorio.',
            'treatment.required' => 'El tratamiento es obligatorio.',
            'medications.required' => 'Debe ingresar al menos dos medicamentos.',
            'medications.array' => 'El formato de medicamentos no es válido.',
            'medications.min' => 'Debe ingresar al menos dos medicamentos.',
            'medications.*.name.required' => 'El medicamento es obligatorio.',
            'medications.*.dose.required' => 'La dosis es obligatoria.',
            'medications.*.frequency_duration.required' => 'La frecuencia/duración es obligatoria.',
        ], [
            'diagnosis' => 'diagnóstico',
            'treatment' => 'tratamiento',
            'notes' => 'notas',
            'medications' => 'medicamentos',
            'medications.*.name' => 'medicamento',
            'medications.*.dose' => 'dosis',
            'medications.*.frequency_duration' => 'frecuencia/duración',
        ]);

        Consultation::updateOrCreate(
            ['appointment_id' => $this->appointment->id],
            [
                'patient_id' => $this->appointment->patient_id,
                'doctor_id' => $this->appointment->doctor_id,
                'diagnosis' => $validated['diagnosis'],
                'treatment' => $validated['treatment'],
                'notes' => $validated['notes'] ?? null,
                'medications' => $validated['medications'],
            ]
        );

        $this->appointment->update(['status' => 2]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Consulta guardada',
            'text' => 'La consulta médica fue registrada correctamente.',
        ]);

        $this->redirectRoute('admin.appointments.index');
    }

    public function getPreviousConsultationsProperty()
    {
        return Consultation::query()
            ->with(['doctor.user', 'appointment'])
            ->where('patient_id', $this->appointment->patient_id)
            ->where('appointment_id', '!=', $this->appointment->id)
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.consultation-manager', [
            'previousConsultations' => $this->previousConsultations,
        ]);
    }
}
