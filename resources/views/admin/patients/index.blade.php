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

            <div x-data="{ uploading: false, progress: 0, jobProgress: 0, showJobProgress: false, pollingInterval: null }" 
                 x-init="
                    pollingInterval = setInterval(() => {
                        fetch('{{ route('admin.patients.import-progress') }}')
                            .then(r => r.json())
                            .then(data => {
                                if (data && typeof data.processed !== 'undefined' && (data.processed > 0 || data.completed)) {
                                    showJobProgress = true;
                                    jobProgress = data.processed;
                                    
                                    if(data.completed) { 
                                        showJobProgress = false; 
                                        clearInterval(pollingInterval);
                                        fetch('{{ route('admin.patients.clear-progress') }}')
                                            .then(() => {
                                                if(typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        title: '¡Terminado!', 
                                                        text: 'La verificación de datos ha concluido.', 
                                                        icon: 'success'
                                                    }).then(() => window.location.reload());
                                                } else {
                                                    window.location.reload(); 
                                                }
                                            });
                                    }
                                } else {
                                    showJobProgress = false;
                                }
                            })
                    }, 2000);
                 "
                 class="w-full lg:w-auto mt-4 lg:mt-0 overflow-hidden">
                <form 
                    action="{{ route('admin.patients.import') }}" 
                    method="POST" 
                    enctype="multipart/form-data" 
                    class="flex flex-col sm:flex-row sm:items-center gap-3 w-full"
                    x-on:submit.prevent="
                        let fileInput = $event.target.querySelector('input[type=file]');
                        if (!fileInput.files.length) {
                            if(typeof Swal !== 'undefined') Swal.fire('Error', 'Por favor, selecciona un archivo antes de importar.', 'error');
                            else alert('Por favor, selecciona un archivo antes de importar.');
                            return;
                        }
                        uploading = true;
                        let formData = new FormData($event.target);
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', $event.target.action);
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.upload.onprogress = e => { 
                            if(e.lengthComputable) progress = Math.round((e.loaded / e.total) * 100); 
                        };
                        xhr.onload = () => { 
                            if(xhr.status >= 200 && xhr.status < 300) { 
                                if(typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'Importación en proceso',
                                        text: 'El archivo fue recibido y se está procesando en segundo plano.',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(() => window.location.reload());
                                } else {
                                    alert('El archivo fue recibido. Recargando...');
                                    window.location.reload();
                                }
                            } else {
                                uploading = false; 
                                let errorMessage = 'Hubo un error inesperado al subir el archivo.';
                                if(xhr.responseText) {
                                    try {
                                        let response = JSON.parse(xhr.responseText);
                                        errorMessage = response.message || errorMessage;
                                        if(response.errors && response.errors.patients_file) {
                                            errorMessage = response.errors.patients_file[0];
                                        }
                                    } catch(e) {}
                                }
                                if(typeof Swal !== 'undefined') Swal.fire('Error', errorMessage, 'error');
                                else alert(errorMessage);
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
                        <span x-text="uploading ? 'Cargando al servidor...' : 'Importar'"></span>
                    </x-wire-button>
                </form>

                <!-- Barra de progreso File Upload -->
                <div x-show="uploading" class="w-full bg-gray-200 rounded-full h-2.5 mt-3 duration-300" x-transition>
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-out" x-bind:style="'width: ' + progress + '%'"></div>
                </div>

                <!-- Barra de progreso Segundo Plano (Job) -->
                <div x-show="showJobProgress" class="mt-4 p-4 bg-white rounded-lg shadow-sm border border-blue-100" x-transition>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold text-blue-800"><i class="fa-solid fa-cogs mr-1"></i> Análisis en segundo plano...</span>
                        <span class="text-xs font-bold text-blue-800" x-text="'Procesando filas: ' + jobProgress"></span>
                    </div>
                    <div class="w-full bg-blue-100 rounded-full h-3 mt-2 overflow-hidden shadow-inner">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300 ease-in-out" style="width: 100%; opacity: 0.5; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Puedes seguir navegando, te notificaremos al terminar.</p>
                </div>
            </div>
        </div>

        @error('patients_file')
            <p class="text-sm text-red-600 mt-3">{{ $message }}</p>
        @enderror
    </x-wire-card>

    @livewire('admin.datatables.patient-table')

</x-admin-layout>
