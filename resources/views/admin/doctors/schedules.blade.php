<x-admin-layout title="Doctores | Horarios" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
    ['name' => 'Horarios'],
]">
    <x-wire-card>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Gestor de horarios</h2>
                <p class="text-sm text-gray-500">Doctor: {{ $doctor->user?->name }}</p>
            </div>
        </div>

        @if($errors->has('selected_slots'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4">
                <p class="text-sm font-semibold text-red-700">{{ $errors->first('selected_slots') }}</p>
                @if(session('schedule_conflicts'))
                    <ul class="mt-2 space-y-1 text-sm text-red-700 list-disc pl-5">
                        @foreach(session('schedule_conflicts') as $conflict)
                            <li>
                                Cita #{{ $conflict['id'] }} · {{ $conflict['date'] }} · {{ $conflict['start_time'] }} - {{ $conflict['end_time'] }} · {{ $conflict['patient'] }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form method="GET" action="{{ route('admin.doctors.schedules.edit', $doctor) }}" class="grid lg:grid-cols-4 gap-3 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                <input type="time" name="start_time" value="{{ $startTime }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jornada (horas)</label>
                <input type="number" name="work_hours" min="1" max="12" value="{{ $workHours }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intervalo (min)</label>
                <input type="number" name="interval" min="5" max="60" value="{{ $interval }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end">
                <x-wire-button type="submit" blue class="w-full">Generar slots</x-wire-button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.doctors.schedules.update', $doctor) }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="start_time" value="{{ $startTime }}">
            <input type="hidden" name="work_hours" value="{{ $workHours }}">
            <input type="hidden" name="interval" value="{{ $interval }}">

            @php
                $days = [
                    1 => 'Lunes',
                    2 => 'Martes',
                    3 => 'Miércoles',
                    4 => 'Jueves',
                    5 => 'Viernes',
                    6 => 'Sábado',
                    7 => 'Domingo',
                ];
                $selectedFromOld = old('selected_slots');
            @endphp

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-gray-600 font-semibold">DÍA/HORA</th>
                            @foreach($days as $dayName)
                                <th class="px-3 py-2 text-left text-gray-600 font-semibold">{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($slots as $slot)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-700">{{ $slot['label'] }}</td>
                                @foreach($days as $dayNumber => $dayName)
                                    <td class="px-3 py-2">
                                        <label class="inline-flex items-center gap-2">
                                            <input
                                                type="checkbox"
                                                name="selected_slots[{{ $dayNumber }}][]"
                                                value="{{ $slot['value'] }}"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                @checked(in_array($slot['value'], is_array($selectedFromOld[$dayNumber] ?? null) ? $selectedFromOld[$dayNumber] : ($selected[$dayNumber] ?? [])))
                                            >
                                            <span class="text-gray-600">Disponible</span>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-6">
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar horario
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
