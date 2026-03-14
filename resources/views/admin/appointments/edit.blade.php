<x-admin-layout title="Citas | Editar" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Editar'],
]">
    <x-wire-card>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Editar cita</h2>

        <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                    <select name="patient_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                {{ $patient->user?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                    <select name="doctor_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
                                {{ $doctor->user?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" name="date" value="{{ old('date', $appointment->date) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                    <input type="time" name="start_time" value="{{ old('start_time', substr($appointment->start_time, 0, 5)) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora fin</label>
                    <input type="time" name="end_time" value="{{ old('end_time', substr($appointment->end_time, 0, 5)) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="1" @selected(old('status', $appointment->status) == 1)>Programado</option>
                    <option value="0" @selected(old('status', $appointment->status) == 0)>Cancelado</option>
                    <option value="2" @selected(old('status', $appointment->status) == 2)>Completado</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la cita</label>
                <textarea name="reason" rows="4" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('reason', $appointment->reason) }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <x-wire-button href="{{ route('admin.appointments.index') }}" gray>Cancelar</x-wire-button>
                <x-wire-button type="submit" blue>Guardar cambios</x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
