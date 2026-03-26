<x-admin-layout 
    title="Pacientes | MediCitas"
    :breadcrumbs="[
        [     
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],  
        [     
            'name' => 'Pacientes',
        ],
    ]">

    <x-wire-card class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Carga masiva de pacientes (CSV/XLSX)</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Sube un archivo grande y el sistema lo procesará en segundo plano con Jobs.
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    Encabezados requeridos: <span class="font-mono">name,email,id_number,phone,address</span>.
                    Opcionales: <span class="font-mono">blood_type,allergies,chronic_conditions,surgical_history,family_history,observations,emergency_contact_name,emergency_contact_phone,emergency_contact_relationship</span>.
                </p>
            </div>

            <form action="{{ route('admin.patients.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input
                    type="file"
                    name="patients_file"
                    accept=".csv,.txt,.xlsx"
                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-white"
                    required
                >
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-file-import"></i>
                    Importar
                </x-wire-button>
            </form>
        </div>

        @error('patients_file')
            <p class="text-sm text-red-600 mt-3">{{ $message }}</p>
        @enderror
    </x-wire-card>

    @livewire('admin.datatables.patient-table')

</x-admin-layout>
