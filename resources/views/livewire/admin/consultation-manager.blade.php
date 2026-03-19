<div x-data="{ previousModal: false, medicalHistoryModal: false }" class="space-y-4">
    @php
        $consultaErrorFields = ['diagnosis', 'treatment', 'notes'];
        $recetaErrorFields = [
            'medications',
            'medications.*.name',
            'medications.*.dose',
            'medications.*.frequency_duration',
        ];
    @endphp

    <x-wire-card>
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Consulta</h2>
                <p class="text-3xl font-bold text-gray-900">{{ $appointment->patient->user->name }}</p>
                <p class="text-lg text-gray-500">DNI: {{ $appointment->patient->user->id_number ?? 'Sin registro' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-wire-button gray sm x-on:click="medicalHistoryModal = true">
                    <i class="fa-solid fa-folder-open me-1"></i>
                    Ver Historia
                </x-wire-button>

                <x-wire-button gray sm x-on:click="previousModal = true">
                    <i class="fa-solid fa-rotate-left me-1"></i>
                    Consultas Anteriores
                </x-wire-button>
            </div>
        </div>
    </x-wire-card>

    <x-wire-card>
        <x-tabs active="consulta">
            <x-slot name="header">
                <x-tabs-link tab="consulta" :error="$errors->hasAny($consultaErrorFields)" icon="fa-solid fa-file-medical">
                    Consulta
                </x-tabs-link>
                <x-tabs-link tab="receta" :error="$errors->hasAny($recetaErrorFields)" icon="fa-solid fa-prescription-bottle-medical">
                    Receta
                </x-tabs-link>
            </x-slot>

            <x-tab-content tab="consulta">
                <div class="space-y-4">
                    <div>
                        <x-wire-textarea label="Diagnóstico" wire:model="diagnosis" rows="5" placeholder="Describa el diagnóstico del paciente aquí..." />
                    </div>

                    <div>
                        <x-wire-textarea label="Tratamiento" wire:model="treatment" rows="4" placeholder="Describa el tratamiento recomendado aquí..." />
                    </div>

                    <div>
                        <x-wire-textarea label="Notas" wire:model="notes" rows="3" placeholder="Agregue notas adicionales sobre la consulta..." />
                    </div>
                </div>
            </x-tab-content>

            <x-tab-content tab="receta">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-700">Medicamentos</h3>
                        <x-wire-button sm gray wire:click="addMedication">
                            <i class="fa-solid fa-plus me-1"></i>
                            + Añadir medicamento
                        </x-wire-button>
                    </div>

                    @foreach($medications as $index => $medication)
                        <div class="grid lg:grid-cols-3 gap-3 p-3 border border-gray-200 rounded-lg">
                            <div>
                                <x-wire-input label="Medicamento" wire:model="medications.{{ $index }}.name" />
                            </div>

                            <div>
                                <x-wire-input label="Dosis" wire:model="medications.{{ $index }}.dose" />
                            </div>

                            <div>
                                <x-wire-input label="Frecuencia / Duración" wire:model="medications.{{ $index }}.frequency_duration" />
                            </div>

                            <div class="lg:col-span-3 flex justify-end">
                                <x-wire-button xs red wire:click="removeMedication({{ $index }})">
                                    Quitar
                                </x-wire-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-tab-content>
        </x-tabs>

        <div class="flex justify-end mt-6">
            <x-wire-button blue wire:click="saveConsultation">
                <i class="fa-solid fa-floppy-disk me-1"></i>
                Guardar consulta
            </x-wire-button>
        </div>
    </x-wire-card>

    <div
        x-show="medicalHistoryModal"
        x-on:keydown.escape.window="medicalHistoryModal = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-black/50" x-on:click="medicalHistoryModal = false"></div>

            <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                    <h3 class="text-lg font-semibold">Historia médica del paciente</h3>
                    <button type="button" class="text-gray-500" x-on:click="medicalHistoryModal = false">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="grid md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Tipo de sangre:</p>
                        <p class="font-semibold">{{ $appointment->patient->bloodType?->name ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Alergias:</p>
                        <p class="font-semibold">{{ $appointment->patient->allergies ?: 'No registradas' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Enfermedades crónicas:</p>
                        <p class="font-semibold">{{ $appointment->patient->chronic_conditions ?: 'No registradas' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Antecedentes quirúrgicos:</p>
                        <p class="font-semibold">{{ $appointment->patient->surgical_history ?: 'No registrados' }}</p>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.patients.show', $appointment->patient) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                        Ver / Editar Historia Médica
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div
        x-show="previousModal"
        x-on:keydown.escape.window="previousModal = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-black/50" x-on:click="previousModal = false"></div>

            <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Consultas Anteriores</h3>
                    <button type="button" class="text-gray-500" x-on:click="previousModal = false">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto space-y-4">
                    @forelse ($previousConsultations as $consultation)
                        <div class="border border-indigo-200 rounded-lg p-4">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        <i class="fa-regular fa-calendar me-1 text-indigo-600"></i>
                                        {{ $consultation->appointment?->date }}
                                    </p>
                                    <p class="text-sm text-gray-600">Atendido por: Dr(a). {{ $consultation->doctor->user->name }}</p>
                                </div>

                                <x-wire-button href="{{ route('admin.consultations.show', $consultation->appointment_id) }}" xs blue>
                                    Consultar detalle
                                </x-wire-button>
                            </div>

                            <div class="bg-gray-50 rounded-md p-3 text-sm">
                                <p><span class="font-semibold">Diagnóstico:</span> {{ $consultation->diagnosis }}</p>
                                <p><span class="font-semibold">Tratamiento:</span> {{ $consultation->treatment }}</p>
                                <p><span class="font-semibold">Notas:</span> {{ $consultation->notes ?: 'Sin notas' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No hay consultas anteriores para este paciente.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
