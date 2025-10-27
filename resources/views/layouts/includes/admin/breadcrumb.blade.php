{{--Verifica si la variable $breadcrumbs tiene elementos--}}
@if (count($breadcrumbs))
    {{--Margin bottom o margen inferior--}}
    <nav class="mb-2 block">
    
        <ol class="flex flex-wrap text-slate-700 text-sm">
            @foreach ($breadcrumbs as $item)
                <li class="flex items-center">
                    {{--Se corrige el doble $ en $loop->first--}}
                    @unless ($loop->first)
                        {{--El span es un separador entre los elementos del breadcrumb--}}
                        <span class="px-2 text-gray-400">/</span>
                    @endunless

                    {{--Revisa si el elemento tiene una llave 'href'--}}
                    @isset($item['href'])
                        <a href="{{ $item['href'] }}" 
                        class="opacity-60 hover:opacity-100 transition">
                        {{ $item['name'] }}
                        </a>
                    @else
                            {{ $item['name'] }}
                    @endisset
                </li>
            @endforeach

        </ol>
            {{-- El último item aparece como negritas --}}
         @if (count($breadcrumbs)>1)
         {{-- mt = margin --}}
            <h6 class="font-bold mt-2">
                {{ end($breadcrumbs)['name'] }}
            </h6>
         @endif
    </nav>
@endif
