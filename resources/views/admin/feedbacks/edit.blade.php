<x-admin-layout 
    title="Editar Sugerencia | MediCitas"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Sugerencias', 'href' => route('admin.feedbacks.index')],
        ['name' => 'Editar'],
    ]">

    <div class="p-4 mt-4">
        <div class="bg-white rounded-lg shadow-md p-6 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-900 mb-2 dark:text-white">Editar Sugerencia</h2>
            <p class="text-gray-600 text-sm mb-6 dark:text-gray-400">Modifique los datos de la sugerencia o actualice su estado.</p>

            <form action="{{ route('admin.feedbacks.update', $feedback) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="nombre_usuario" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nombre del usuario <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nombre_usuario" 
                               id="nombre_usuario" 
                               value="{{ old('nombre_usuario', $feedback->nombre_usuario) }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="Ingrese su nombre completo"
                               required>
                        @error('nombre_usuario')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="tipo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Tipo <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo" 
                                    id="tipo" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                                <option value="">Seleccione un tipo</option>
                                <option value="Queja" {{ old('tipo', $feedback->tipo) == 'Queja' ? 'selected' : '' }}>Queja</option>
                                <option value="Sugerencia" {{ old('tipo', $feedback->tipo) == 'Sugerencia' ? 'selected' : '' }}>Sugerencia</option>
                                <option value="Felicitación" {{ old('tipo', $feedback->tipo) == 'Felicitación' ? 'selected' : '' }}>Felicitación</option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select name="estado" 
                                    id="estado" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                                <option value="Pendiente" {{ old('estado', $feedback->estado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="Revisado" {{ old('estado', $feedback->estado) == 'Revisado' ? 'selected' : '' }}>Revisado</option>
                                <option value="Resuelto" {{ old('estado', $feedback->estado) == 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="comentario" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Comentario detallado <span class="text-red-500">*</span>
                        </label>
                        <textarea name="comentario" 
                                  id="comentario" 
                                  rows="6"
                                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                  placeholder="Describa detalladamente su queja, sugerencia o felicitación..."
                                  required>{{ old('comentario', $feedback->comentario) }}</textarea>
                        @error('comentario')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('admin.feedbacks.index') }}" 
                           class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            <i class="fa-solid fa-floppy-disk mr-2"></i>Actualizar Sugerencia
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
