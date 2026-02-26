{{--L+ogica de PHP para manejar errores y controlar la pestaña activa--}}

@php
    //Definimos qué campos pertenecen a cada pestaña
    $errorGroups = [
        'antecedentes' => ['allergies', 'chronic_conditions', 'surgical_history', 'family_history'],
        'informacion-general' => ['blood_type_id', 'observations'],
        'contacto-emergencia' => [
            'emergency_contact_name', 
            'emergency_contact_phone', 
            'emergency_contact_relationship'],
    ];
    //Pestaña por defecto
    $initialTab = 'datos personales';

    //Si hay errores, búscamos en qué grupo están para abirr esa pestaña
    foreach ($errorGroups as $tabName => $fields) {
        if ($errors->hasAny($fields)) {
            $initialTab = $tabName;
            break; //Salimos deld bucle una vez encontramos la primera pestaña con errores
        }
    }
@endphp

<x-admin-layout 
    title="Pacientes | MediCitas"
    :breadcrumbs="[
        [     
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],  
        [     
            'name' => 'Pacientes',
            'href' => route('admin.patients.index')
        ],
        [
            'name' => 'Editar'
        ]  
    ]">

<form action="{{route('admin.patients.update', $patient)}}" method="POST">
    @csrf
    @method('PUT')
    {{--Encabezado con foto y accciones--}}
    <x-wire-card class="mb-8">
        <div class="lg:flex lg:justify-between lg:items-center">
            <div class="flex items-center">
                <img src="{{ $patient->profile_photo_url }}" alt="{{ $patient->name }}"
                class="h-20 w-20 rounded-full object-cover">
                <div>
                    <p class="text-2xl font-bold text-gray-900 ml-4">{{ $patient->user->name }}</p>
                </div>
            </div>
            <div class="flex space-x-3 mt-6 lg:mt-0">
                <x-wire-button outline gray href="{{ route('admin.patients.index') }}">Volver</x-wire-button>
                <x-wire-button type="submit">
                    <i class="fa-solid fa-check"></i>
                    Guardar cambios
                </x-wire-button>
            </div>
        </div>
    </x-wire-card>
    {{--Tabs de navegacion--}}
    <x-wire-card>
        <x-tabs :active="$initialTab">
            <x-slot name="header">
                {{--Tab 1: Datos personales--}}
                <x-tabs-link tab="datos personales" icon="fa-solid fa-user">
                    Datos personales
                </x-tabs-link>

                {{--Tab 2: Antecedentes médicos--}}
                <x-tabs-link tab="antecedentes" 
                    :error="$errors->hasAny($errorGroups['antecedentes'])" 
                    icon="fa-solid fa-file-lines">
                    Antecedentes
                </x-tabs-link>

                {{--Tab 3: Información general--}}
                <x-tabs-link tab="informacion-general" 
                    :error="$errors->hasAny($errorGroups['informacion-general'])" 
                    icon="fa-solid fa-info">
                    Información general
                </x-tabs-link>

                {{--Tab 4: Contacto de emergencia--}}
                <x-tabs-link tab="contacto-emergencia" 
                    :error="$errors->hasAny($errorGroups['contacto-emergencia'])" 
                    icon="fa-solid fa-heart">
                    Contacto de emergencia
                </x-tabs-link>
            </x-slot>

            {{--Contenido del tab 1: Datos personales--}}
            <x-tab-content tab="datos personales">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    {{--Lado izquiero Información--}}
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-user-gear text-blue-500 text-xl mt-1"></i>
                            </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-blue-800">
                                        Edición de cuenta de usuario</h3>
                                        <div class="mt-1 text-sm text-blue-600">
                                            <p>La <strong>información de acceso</strong>(nombre, correo electrónico, contraseña) 
                                                debe gestionarse desde la cuenta de usuario asociada.</p>
                                        </div>
                                </div>
                        </div>
                        {{--Lado derecho Botón de acción--}}
                        <div class="flex-shrink-0">
                            <x-wire-button primary sm href="{{ route('admin.users.edit', $patient->user) }}"
                                target="_blank">
                                <i class="fa-solid fa-arrow-up-right-from-square ms-2"></i>
                                Editar usuario

                            </x-wire-button>
                        </div>
                    </div>
                </div>
                <div class="grid lg:grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-500 font-semibold ml-2">Teléfono</span>
                        <span class="text-gray-900 text-sm ml-2">{{ $patient->user->phone }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 font-semibold ml-2">Email</span>
                        <span class="text-gray-900 text-sm ml-2">{{ $patient->user->email }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 font-semibold ml-2">Dirección</span>
                        <span class="text-gray-900 text-sm ml-2">{{ $patient->user->address }}</span>
                    </div>
                </div>
            </x-tab-content>

            {{--Contenido de Tab 2: Antecedentes--}}
            <x-tab-content tab="antecedentes">
                <div class="grid lg:grid-cols-2 gap-4">
                    <div>
                        <x-wire-textarea label="Alergias conocidas" name="allergies">
                            {{ old('allergies', $patient->allergies) }}
                        </x-wire-textarea>
                    </div>
                    <div>
                        <x-wire-textarea label="Enfermedades cónicas" name="chronic_conditions">
                            {{ old('chronic_conditions', $patient->chronic_conditions) }}
                        </x-wire-textarea>
                    </div>
                    <div>
                        <x-wire-textarea label="Antecedentes quirúrgicos" name="surgical_history">
                            {{ old('surgical_history', $patient->surgical_history) }}
                        </x-wire-textarea>
                    </div>
                    <div>
                        <x-wire-textarea label="Antecedentes familiares" name="family_history">
                            {{ old('family_history', $patient->family_history) }}
                        </x-wire-textarea>
                    </div>
                </div>
            </x-tab-content>

            {{--Contenido de Tab 3: Información general--}}
            <x-tab-content tab="informacion-general">
                <x-wire-native-select label="Tipo de sangre" class="mb-4" 
                name="blood_type_id">
                <option value="">Seleccione un tipo de sangre</option>
                    @foreach($bloodTypes as $bloodType)
                        <option value="{{ $bloodType->id }}" @selected(old('blood_type_id', $patient->blood_type_id) == $bloodType->id )>
                            {{ $bloodType->name }}
                        </option> 
                    @endforeach
            </x-wire-native-select>
            <x-wire-textarea label="Observaciones" name="observations">
                {{ old('observations', $patient->observations) }}
            </x-wire-textarea>
            </x-tab-content>

            {{--Contenido de Tab 4: Contacto de emergencia--}}
            <x-tab-content tab="contacto-emergencia">
                <div class="space-y-4">
                    <div>
                        <x-wire-input label="Nombre de contacto" name="emergency_contact_name" 
                            value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" />
                    </div>
                    <div>
                        <x-wire-phone label="Telefono de contacto" name="emergency_contact_phone" 
                            placeholder="(999) 999-9999"
                            value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" />
                    </div>
                    <div>
                        <x-wire-input label="Relacion con el contacto" name="emergency_contact_relationship" 
                            aria-placeholder="Ej: Padre, Madre, Hermano(a), Amigo(a), etc."
                            value="{{ old('emergency_contact_relationship', $patient->emergency_contact_relationship) }}" />
                    </div>
                </div>
            </x-tab-content>
        </x-tabs>
    </x-wire-card>
</form>

</x-admin-layout>
