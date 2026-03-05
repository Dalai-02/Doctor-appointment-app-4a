<x-admin-layout 
    title="Buzón de Sugerencias | MediCitas"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Sugerencias'],
    ]">

    <x-slot name="action">
        <a href="{{ route('admin.feedbacks.create') }}" 
           class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            <i class="fa-solid fa-plus mr-2"></i>Nueva Sugerencia
        </a>
    </x-slot>

    <div class="p-4 mt-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Buzón de Sugerencias</h1>
        
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Usuario</th>
                        <th scope="col" class="px-6 py-3">Tipo</th>
                        <th scope="col" class="px-6 py-3">Estado</th>
                        <th scope="col" class="px-6 py-3">Fecha</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $feedback)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $feedback->id }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $feedback->nombre_usuario }}
                            </td>
                            <td class="px-6 py-4">
                                @switch($feedback->tipo)
                                    @case('Queja')
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $feedback->tipo }}
                                        </span>
                                        @break
                                    @case('Sugerencia')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ $feedback->tipo }}
                                        </span>
                                        @break
                                    @case('Felicitación')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $feedback->tipo }}
                                        </span>
                                        @break
                                    @default
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                            {{ $feedback->tipo }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                @switch($feedback->estado)
                                    @case('Pendiente')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                                            {{ $feedback->estado }}
                                        </span>
                                        @break
                                    @case('Revisado')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ $feedback->estado }}
                                        </span>
                                        @break
                                    @case('Resuelto')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $feedback->estado }}
                                        </span>
                                        @break
                                    @default
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                            {{ $feedback->estado }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                {{ $feedback->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.feedbacks.edit', $feedback) }}" 
                                       class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.feedbacks.destroy', $feedback) }}" 
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
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No hay sugerencias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>
