<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [   'name' => 'Profile',
        'href' => route('admin.dashboard')
    ],
]">
    Hola desde admin
</x-admin-layout>