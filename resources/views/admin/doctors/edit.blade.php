<x-admin-layout title="Editar Doctor | {{ $doctor->user->name }}">
    
    {{-- Formulario con Método PUT --}}
    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf
        @method('PUT')

        {{-- Cabecera de Acciones --}}
        <x-wire-card class="mt-10">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center gap-4">
                    <img src="{{ $doctor->user->profile_photo_url }}" class="h-20 w-20 rounded-full object-cover">
                    <div>
                        <p class="text-2xl font-semibold text-gray-900">{{ $doctor->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $doctor->user->email }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button href="{{ route('admin.doctors.index') }}" white>Volver</x-wire-button>
                    <x-wire-button type="submit" blue x-bind:disabled="submitting">
                        <span x-show="!submitting">
                            <i class="fa-solid fa-floppy-disk mr-2"></i>
                            Guardar cambios
                        </span>
                        <span x-show="submitting" style="display: none;">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                            Guardando...
                        </span>
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Formulario de Edición --}}
        <x-wire-card class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">
                <i class="fa-solid fa-user-doctor mr-2"></i>
                Información Profesional
            </h3>

            <div class="space-y-6">
                {{-- Especialidad --}}
                <div>
                    <x-wire-native-select name="speciality_id" label="Especialidad">
                        <option value="">Seleccione una especialidad</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                {{-- Número de Licencia --}}
                <div>
                    <x-wire-input
                        name="license_number"
                        label="Número de Licencia"
                        :value="old('license_number', $doctor->license_number)"
                        placeholder="Ej. 123456"
                        type="text"
                        maxlength="8"
                    />
                </div>

                {{-- Biografía --}}
                <div>
                    <x-wire-textarea
                        name="biography"
                        label="Biografía"
                        placeholder="Escriba una breve biografía del doctor..."
                        rows="5"
                        maxlength="255"
                    >{{ old('biography', $doctor->biography) }}</x-wire-textarea>
                    <p class="text-xs text-gray-400 text-right mt-1">Máximo 255 caracteres</p>
                </div>
            </div>
        </x-wire-card>
    </form>
</x-admin-layout>
