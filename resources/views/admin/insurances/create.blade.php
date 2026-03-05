<x-admin-layout 
    title="Nueva Aseguradora | MediCitas"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Aseguradoras', 'href' => route('admin.insurances.index')],
        ['name' => 'Nueva Aseguradora'],
    ]">

    <div class="p-4 mt-4">
        <div class="bg-white rounded-lg shadow-md p-6 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-900 mb-2 dark:text-white">Registrar Aseguradora</h2>
            <p class="text-gray-600 text-sm mb-6 dark:text-gray-400">Complete los datos de la aseguradora o convenio médico.</p>

            <form action="{{ route('admin.insurances.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="nombre_empresa" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nombre de la empresa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nombre_empresa" 
                               id="nombre_empresa" 
                               value="{{ old('nombre_empresa') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="Ej. Seguros Médicos SA"
                               required>
                        @error('nombre_empresa')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono_contacto" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Teléfono de contacto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="telefono_contacto" 
                               id="telefono_contacto" 
                               value="{{ old('telefono_contacto') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="Ej. +52 555 123 4567"
                               required>
                        @error('telefono_contacto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notas_adicionales" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Descripción detallada / Notas adicionales
                        </label>
                        <textarea name="notas_adicionales" 
                                  id="notas_adicionales" 
                                  rows="6"
                                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                  placeholder="Información adicional sobre el convenio, cobertura, etc.">{{ old('notas_adicionales') }}</textarea>
                        @error('notas_adicionales')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('admin.insurances.index') }}" 
                           class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar Aseguradora
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
