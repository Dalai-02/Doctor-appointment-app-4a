<div class="flex items-center gap-2">
    <x-wire-button href="{{ route('admin.appointments.show', $appointment) }}" gray xs>
        <i class="fa-solid fa-eye"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs>
        <i class="fa-solid fa-edit"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.consultations.show', $appointment) }}" emerald xs>
        <i class="fa-solid fa-stethoscope"></i>
    </x-wire-button>
</div>
