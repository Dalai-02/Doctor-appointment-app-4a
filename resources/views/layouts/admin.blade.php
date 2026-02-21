@props([
    'title'=>config('app.name', 'Laravel') ,
    'breadcrumbs'=>[]])
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/0d20d99f15.js" crossOrigin="anonymous"></script>

    <!-- Sweet Alert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{--wireUi--}}
    <wireui:scripts />
    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">

@include('layouts.includes.admin.navigation')

@include('layouts.includes.admin.sidebar')

<div class="p-4 sm:ml-64">
    <div class="mt-14 flex items-center justify-between w-full">

        {{-- Esto muestra las migas de pan a la izquierda --}}
        @include('layouts.includes.admin.breadcrumb')

        {{-- ESTA ES LA LÍNEA QUE FALTABA --}}
        {{-- Esto imprimirá tu botón a la derecha --}}
        <div>
            {{ $action ?? '' }}
        </div>

    </div>

    {{-- El slot principal (la tabla) se imprime debajo --}}
    {{$slot}}
</div>

@stack('modals')

@livewireScripts

<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

{{-- Mostrar Sweet Alert de sesión --}}
@if (session('swal'))
<script>
    Swal.fire(@json(session('swal')));
</script>
@endif

{{-- Mostrar Sweet Alert para errores de validación --}}
@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error de validación',
        text: 'No se ha llenado de manera correcta los campos',
        confirmButtonColor: '#3085d6'
    });
</script>
@endif

<script>
    // Buscar todos los formularios con clase .delete-form
    const forms = document.querySelectorAll('.delete-form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Evita envío inmediato

            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esta acción!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Enviar si confirma
                }
            });
        });
    });
</script>


</body>


</html>