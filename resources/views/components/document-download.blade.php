@props(['url' => null])

@if($url)
    <a href="{{ $url }}" 
       target="_blank"
       class="inline-flex items-center text-primary-600 hover:text-primary-800">
        <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-1" />
        Descargar
    </a>
@else
    <span class="text-gray-500">Sin archivo</span>
@endif
