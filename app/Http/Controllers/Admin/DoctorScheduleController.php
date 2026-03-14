<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function edit(Request $request, Doctor $doctor)
    {
        $startTime = $request->input('start_time', '08:00');
        $workHours = (int) $request->input('work_hours', 8);
        $interval = (int) $request->input('interval', 15);

        $workHours = $workHours > 0 ? $workHours : 8;
        $interval = $interval > 0 ? $interval : 15;

        $slots = $this->generateSlots($startTime, $workHours, $interval);

        $selected = $doctor->schedules()
            ->get()
            ->groupBy('day_of_week')
            ->map(fn ($items) => $items->map(fn ($slot) => $slot->start_time . '-' . $slot->end_time)->values()->all())
            ->toArray();

        return view('admin.doctors.schedules', compact('doctor', 'slots', 'selected', 'startTime', 'workHours', 'interval'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'work_hours' => 'required|integer|min:1|max:12',
            'interval' => 'required|integer|min:5|max:60',
            'selected_slots' => 'nullable|array',
        ]);

        $selectedSlotsByDay = collect($validated['selected_slots'] ?? [])
            ->map(fn ($slots) => is_array($slots) ? array_values($slots) : [])
            ->toArray();

        $conflictingAppointments = $doctor->appointments()
            ->with('patient.user')
            ->whereDate('date', '>=', now()->toDateString())
            ->where('status', 1)
            ->get()
            ->filter(function (Appointment $appointment) use ($selectedSlotsByDay) {
                $dayOfWeek = Carbon::parse($appointment->date)->dayOfWeekIso;
                $slotValue = substr($appointment->start_time, 0, 5) . '-' . substr($appointment->end_time, 0, 5);

                return !in_array($slotValue, $selectedSlotsByDay[$dayOfWeek] ?? [], true);
            })
            ->values();

        if ($conflictingAppointments->isNotEmpty()) {
            $conflicts = $conflictingAppointments->map(function (Appointment $appointment) {
                return [
                    'id' => $appointment->id,
                    'date' => $appointment->date,
                    'start_time' => substr($appointment->start_time, 0, 5),
                    'end_time' => substr($appointment->end_time, 0, 5),
                    'patient' => $appointment->patient?->user?->name ?? 'Paciente sin nombre',
                ];
            })->all();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'selected_slots' => 'No se pudo guardar: el nuevo horario dejaría citas programadas fuera de disponibilidad.',
                ])
                ->with('schedule_conflicts', $conflicts);
        }

        $doctor->schedules()->delete();

        foreach (($validated['selected_slots'] ?? []) as $day => $slots) {
            if (!is_array($slots)) {
                continue;
            }

            foreach ($slots as $slot) {
                [$slotStart, $slotEnd] = explode('-', $slot);

                $doctor->schedules()->create([
                    'day_of_week' => (int) $day,
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                ]);
            }
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Horarios actualizados',
            'text' => 'Los horarios del doctor se guardaron correctamente.',
        ]);

        return redirect()->route('admin.doctors.schedules.edit', $doctor);
    }

    private function generateSlots(string $startTime, int $workHours, int $interval): array
    {
        $slots = [];
        $start = Carbon::createFromFormat('H:i', $startTime);
        $totalMinutes = $workHours * 60;
        $slotCount = (int) floor($totalMinutes / $interval);

        for ($index = 0; $index < $slotCount; $index++) {
            $slotStart = $start->copy()->addMinutes($index * $interval);
            $slotEnd = $slotStart->copy()->addMinutes($interval);

            $slots[] = [
                'value' => $slotStart->format('H:i') . '-' . $slotEnd->format('H:i'),
                'label' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
            ];
        }

        return $slots;
    }
}
