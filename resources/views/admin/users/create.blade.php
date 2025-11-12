<x-admin-layout 
    title="Nuevo usuario | MediCitas"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Usuarios',
            'href' => route('admin.users.index'),
        ],
        [
            'name' => 'Nuevo',
        ],
    ]">

    <div class="p-6 bg-white rounded-lg shadow">
        <p class="text-gray-600 text-center">Formulario para crear usuarios (en construcción)</p>
    </div>

</x-admin-layout>
