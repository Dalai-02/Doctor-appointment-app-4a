<x-admin-layout 
    title="Aseguradoras | MediCitas"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Aseguradoras'],
    ]">

    <x-slot name="action">
        <a href="{{ route('admin.insurances.create') }}" 
           class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            <i class="fa-solid fa-plus mr-2"></i>Nueva Aseguradora
        </a>
    </x-slot>

    <div class="p-4 mt-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Aseguradoras</h1>
        
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Nombre de la Empresa</th>
                        <th scope="col" class="px-6 py-3">Teléfono de Contacto</th>
                        <th scope="col" class="px-6 py-3">Fecha de Registro</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insurances as $insurance)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $insurance->id }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $insurance->nombre_empresa }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $insurance->telefono_contacto }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $insurance->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.insurances.edit', $insurance) }}" 
                                       class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.insurances.destroy', $insurance) }}" 
                                          method="POST" 
                                          class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No hay aseguradoras registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>
