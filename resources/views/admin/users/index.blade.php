<x-admin-layout 
    title="Usuarios | MediCitas"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Usuarios',
        ],
    ]">

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.users.create') }}">
            <i class="fa-solid fa-plus"></i> Nuevo
        </x-wire-button>
    </x-slot>

    <div class="p-6 bg-white rounded-lg shadow">
        <p class="text-gray-600 text-center">Zona de usuarios (vacía por ahora)</p>
    </div>

</x-admin-layout>
