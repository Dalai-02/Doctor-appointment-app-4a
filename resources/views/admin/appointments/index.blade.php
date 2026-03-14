<x-admin-layout title="Citas | Healthify" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas'],
]">
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.appointments.create') }}" blue>
            <i class="fa-solid fa-plus"></i>
            Nueva cita
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <form method="GET" class="grid lg:grid-cols-3 gap-3 mb-4">
            <div>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Buscar"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>
            <div>
                <select name="per_page" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 registros</option>
                    <option value="25" @selected($perPage == 25)>25 registros</option>
                    <option value="50" @selected($perPage == 50)>50 registros</option>
                </select>
            </div>
            <div>
                <x-wire-button type="submit" blue class="w-full">Buscar</x-wire-button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Paciente</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Doctor</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Hora inicio</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Hora fin</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($appointments as $appointment)
                        <tr>
                            <td class="px-4 py-3">{{ $appointment->id }}</td>
                            <td class="px-4 py-3">{{ $appointment->patient?->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $appointment->doctor?->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $appointment->date }}</td>
                            <td class="px-4 py-3">{{ $appointment->start_time }}</td>
                            <td class="px-4 py-3">{{ substr($appointment->end_time, 0, 5) }}</td>
                            <td class="px-4 py-3">{{ $appointment->status_label }}</td>
                            <td class="px-4 py-3">
                                @include('admin.appointments.actions', ['appointment' => $appointment])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                No hay citas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    </x-wire-card>
</x-admin-layout>
