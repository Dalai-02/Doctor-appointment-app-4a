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
                    Encabezados requeridos: <span class="font-mono bg-gray-100 p-1 rounded">nombre_completo, correo, telefono, fecha_nacimiento, tipo_sangre, alergias</span>.
                </p>
            </div>

            <form action="{{ route('admin.patients.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto mt-4 lg:mt-0 overflow-hidden">
                @csrf
                <div class="relative flex-1 min-w-0">
                    <input
                        type="file"
                        name="patients_file"
                        accept=".csv,.txt,.xlsx"
                        class="block w-full text-sm text-slate-500
                               file:mr-4 file:py-2.5 file:px-4
                               file:rounded-l-lg file:border-0
                               file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100
                               cursor-pointer bg-gray-50 border border-gray-300 rounded-lg overflow-hidden"
                        required
                    >
                </div>
                <x-wire-button type="submit" blue class="shrink-0">
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
