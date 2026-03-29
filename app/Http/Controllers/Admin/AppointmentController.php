<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Speciality;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $appointments = Appointment::query()
            ->with(['patient.user', 'doctor.user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%")
                        ->orWhereHas('patient.user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('doctor.user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('date')
            ->latest('start_time')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'search', 'perPage'));
    }

    public function create(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $startTime = $request->input('start_time');
        $specialityId = $request->input('speciality_id');

        $patients = Patient::query()->with('user')->get();
        $specialities = Speciality::query()->orderBy('name')->get();
        $availableDoctors = $this->getAvailableDoctors($date, $startTime, $specialityId);

        return view('admin.appointments.create', compact('patients', 'specialities', 'availableDoctors', 'date', 'startTime', 'specialityId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string',
        ]);

        $start = Carbon::createFromFormat('H:i', $validated['start_time']);
        $end = Carbon::createFromFormat('H:i', $validated['end_time']);
        $duration = $start->diffInMinutes($end);

        if (!$this->isSlotAvailable($validated['doctor_id'], $validated['date'], $validated['start_time'], $validated['end_time'])) {
            throw ValidationException::withMessages([
                'start_time' => 'El horario seleccionado no está disponible para este doctor.',
            ]);
        }

        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'duration' => $duration,
            'reason' => $validated['reason'],
            'status' => 1,
        ]);

        $appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);
        
        try {
            // Forzado temporalmente: Enviar siempre a este correo sin importar qué paciente elijas
            \Illuminate\Support\Facades\Mail::to('dalaipacheco3@gmail.com')
                ->send(new \App\Mail\AppointmentReceipt($appointment));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando comprobante PDF: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Appointment created successfully')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Appointment created successfully',
            ]);
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);

        $patients = Patient::query()->with('user')->get();
        $doctors = Doctor::query()->with(['user', 'speciality'])->get();

        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string',
            'status' => 'required|in:0,1,2',
        ]);

        $start = Carbon::createFromFormat('H:i', $validated['start_time']);
        $end = Carbon::createFromFormat('H:i', $validated['end_time']);
        $duration = $start->diffInMinutes($end);

        if (!$this->isSlotAvailable($validated['doctor_id'], $validated['date'], $validated['start_time'], $validated['end_time'], $appointment->id)) {
            throw ValidationException::withMessages([
                'start_time' => 'El horario seleccionado no está disponible para este doctor.',
            ]);
        }

        $appointment->update([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'duration' => $duration,
            'reason' => $validated['reason'],
            'status' => (int) $validated['status'],
        ]);

        return redirect()
            ->route('admin.appointments.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Cita actualizada correctamente',
            ]);
    }

    public function show(Appointment $appointment)
    {
        return redirect()->route('admin.consultations.show', $appointment);
    }

    public function consultation(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'consultation']);

        return view('admin.appointments.show', compact('appointment'));
    }

    private function getAvailableDoctors(string $date, ?string $startTime, ?string $specialityId)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso;

        $doctors = Doctor::query()
            ->with(['user', 'speciality'])
            ->when($specialityId, fn ($q) => $q->where('speciality_id', $specialityId))
            ->get();

        return $doctors->map(function (Doctor $doctor) use ($dayOfWeek, $date, $startTime) {
            $scheduleSlots = DoctorSchedule::query()
                ->where('doctor_id', $doctor->id)
                ->where('day_of_week', $dayOfWeek)
                ->orderBy('start_time')
                ->get(['start_time', 'end_time'])
                ->map(fn ($slot) => [
                    'start_time' => substr($slot->start_time, 0, 5),
                    'end_time' => substr($slot->end_time, 0, 5),
                ]);

            $availableSlots = $scheduleSlots->filter(function ($slot) use ($doctor, $date) {
                return !$this->hasOverlap($doctor->id, $date, $slot['start_time'], $slot['end_time']);
            });

            if ($startTime) {
                $availableSlots = $availableSlots->filter(fn ($slot) => $slot['start_time'] === $startTime);
            }

            return [
                'id' => $doctor->id,
                'name' => $doctor->user?->name,
                'speciality' => $doctor->speciality?->name,
                'speciality_id' => $doctor->speciality_id,
                'slots' => $availableSlots->values()->all(),
            ];
        })->filter(fn ($doctor) => count($doctor['slots']) > 0)->values();
    }

    private function isSlotAvailable(int $doctorId, string $date, string $startTime, string $endTime, ?int $ignoreAppointmentId = null): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso;

        $existsInSchedule = DoctorSchedule::query()
            ->where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', $startTime)
            ->where('end_time', $endTime)
            ->exists();

        if (!$existsInSchedule) {
            return false;
        }

        return !$this->hasOverlap($doctorId, $date, $startTime, $endTime, $ignoreAppointmentId);
    }

    private function hasOverlap(int $doctorId, string $date, string $startTime, string $endTime, ?int $ignoreAppointmentId = null): bool
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where('date', $date)
            ->when($ignoreAppointmentId, fn ($q) => $q->where('id', '!=', $ignoreAppointmentId))
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }
}
