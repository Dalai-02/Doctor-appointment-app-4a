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

            <div x-data="{ uploading: false, progress: 0 }" class="w-full lg:w-auto mt-4 lg:mt-0 overflow-hidden">
                <form 
                    action="{{ route('admin.patients.import') }}" 
                    method="POST" 
                    enctype="multipart/form-data" 
                    class="flex flex-col sm:flex-row sm:items-center gap-3 w-full"
                    x-on:submit.prevent="
                        uploading = true;
                        let formData = new FormData($event.target);
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', $event.target.action);
                        xhr.upload.onprogress = e => { 
                            if(e.lengthComputable) progress = Math.round((e.loaded / e.total) * 100); 
                        };
                        xhr.onload = () => { 
                            if(xhr.status >= 200 && xhr.status < 300) { 
                                window.location.href = xhr.responseURL || window.location.href; 
                            } else { 
                                uploading = false; 
                                alert('Hubo un error al subir el archivo.'); 
                            } 
                        };
                        xhr.send(formData);
                    "
                >
                    @csrf
                    <div class="relative flex-1 min-w-0">
                        <input
                            type="file"
                            name="patients_file"
                            accept=".csv,.txt,.xlsx"
                            x-bind:disabled="uploading"
                            class="block w-full text-sm text-slate-500
                                   file:mr-4 file:py-2.5 file:px-4
                                   file:rounded-l-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100
                                   cursor-pointer bg-gray-50 border border-gray-300 rounded-lg overflow-hidden disabled:opacity-50"
                            required
                        >
                    </div>
                    <x-wire-button type="submit" blue class="shrink-0" x-bind:disabled="uploading">
                        <i class="fa-solid fa-file-import" x-show="!uploading"></i>
                        <svg x-show="uploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="uploading ? 'Subiendo...' : 'Importar'"></span>
                    </x-wire-button>
                </form>

                <!-- Barra de progreso -->
                <div x-show="uploading" class="w-full bg-gray-200 rounded-full h-2.5 mt-3 duration-300" x-transition>
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-out" x-bind:style="'width: ' + progress + '%'"></div>
                </div>
                <div x-show="uploading" class="text-xs text-gray-500 text-right mt-1" x-text="progress + '%'"></div>
            </div>
        </div>

        @error('patients_file')
            <p class="text-sm text-red-600 mt-3">{{ $message }}</p>
        @enderror
    </x-wire-card>

    @livewire('admin.datatables.patient-table')

</x-admin-layout>
