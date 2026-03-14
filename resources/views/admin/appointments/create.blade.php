<x-admin-layout title="Citas | Crear" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Nueva'],
]">
    <x-wire-card class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Buscar disponibilidad</h2>
        <p class="text-sm text-gray-600 mb-4">Encuentra el horario perfecto para tu cita.</p>

        <form method="GET" action="{{ route('admin.appointments.create') }}" class="grid lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <input type="date" name="date" value="{{ $date }}" min="{{ now()->toDateString() }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                <input type="time" name="start_time" value="{{ $startTime }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad (opcional)</label>
                <select name="speciality_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas</option>
                    @foreach($specialities as $speciality)
                        <option value="{{ $speciality->id }}" @selected((string)$specialityId === (string)$speciality->id)>{{ $speciality->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <x-wire-button type="submit" blue class="w-full">Buscar disponibilidad</x-wire-button>
            </div>
        </form>
    </x-wire-card>

    <form
        action="{{ route('admin.appointments.store') }}"
        method="POST"
        class="grid lg:grid-cols-3 gap-6"
        x-data="{
            selectedDoctorId: '{{ old('doctor_id') }}',
            selectedDoctorName: '',
            selectedStart: '{{ old('start_time', $startTime) }}',
            selectedEnd: '{{ old('end_time') }}',
            pickSlot(doctorId, doctorName, startTime, endTime) {
                this.selectedDoctorId = doctorId;
                this.selectedDoctorName = doctorName;
                this.selectedStart = startTime;
                this.selectedEnd = endTime;
            },
            displayRange() {
                return this.selectedStart && this.selectedEnd ? `${this.selectedStart} - ${this.selectedEnd}` : '--:--';
            },
            durationMinutes() {
                if (!this.selectedStart || !this.selectedEnd) {
                    return '--';
                }

                const [startHour, startMinute] = this.selectedStart.split(':').map(Number);
                const [endHour, endMinute] = this.selectedEnd.split(':').map(Number);

                if (
                    Number.isNaN(startHour) || Number.isNaN(startMinute) ||
                    Number.isNaN(endHour) || Number.isNaN(endMinute)
                ) {
                    return '--';
                }

                const startTotal = (startHour * 60) + startMinute;
                const endTotal = (endHour * 60) + endMinute;
                const diff = endTotal - startTotal;

                return diff > 0 ? `${diff} minutos` : '--';
            },
            initials(name) {
                const parts = name.split(' ').filter(Boolean);
                return ((parts[0]?.[0] ?? '') + (parts[1]?.[0] ?? '')).toUpperCase();
            }
        }"
    >
        @csrf

        <input type="hidden" name="doctor_id" x-model="selectedDoctorId">
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="start_time" x-model="selectedStart">
        <input type="hidden" name="end_time" x-model="selectedEnd">

        <div class="lg:col-span-2 space-y-4">
            @forelse($availableDoctors as $doctor)
                <x-wire-card>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold text-lg">
                            {{ strtoupper(substr($doctor['name'], 0, 1)) }}{{ strtoupper(substr(explode(' ', $doctor['name'])[1] ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $doctor['name'] }}</h3>
                            <p class="text-sm text-indigo-600">{{ $doctor['speciality'] ?? 'Sin especialidad' }}</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 mb-2">Horarios disponibles:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($doctor['slots'] as $slot)
                            <button
                                type="button"
                                x-on:click="pickSlot('{{ $doctor['id'] }}', '{{ $doctor['name'] }}', '{{ $slot['start_time'] }}', '{{ $slot['end_time'] }}')"
                                class="px-3 py-1.5 rounded-md text-sm bg-indigo-100 text-indigo-700 hover:bg-indigo-600 hover:text-white transition"
                            >
                                {{ $slot['start_time'] }} - {{ $slot['end_time'] }}
                            </button>
                        @endforeach
                    </div>
                </x-wire-card>
            @empty
                <x-wire-card>
                    <p class="text-sm text-gray-500">No hay doctores disponibles para la fecha/filtros seleccionados.</p>
                </x-wire-card>
            @endforelse
        </div>

        <div>
            <x-wire-card>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumen de la cita</h3>

                <dl class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">Doctor</dt>
                        <dd class="font-semibold" x-text="selectedDoctorName || 'Sin seleccionar'"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">Fecha</dt>
                        <dd class="font-semibold">{{ $date }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">Horario</dt>
                        <dd class="font-semibold" x-text="displayRange()"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">Duración</dt>
                        <dd class="font-semibold" x-text="durationMinutes()"></dd>
                    </div>
                </dl>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                        <select name="patient_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Seleccione un paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>{{ $patient->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la cita</label>
                        <textarea name="reason" rows="4" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('reason') }}</textarea>
                    </div>

                    <x-wire-button type="submit" blue class="w-full">Confirmar cita</x-wire-button>
                </div>
            </x-wire-card>
        </div>
    </form>
</x-admin-layout>
