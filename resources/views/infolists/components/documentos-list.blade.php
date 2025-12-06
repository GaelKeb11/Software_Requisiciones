<div class="space-y-2">
    @foreach ($getRecord()->documentos as $documento)
        <div class="flex items-center justify-between p-2 border rounded-lg">
            <div class="break-all">
                <p class="font-semibold">{{ $documento->nombre_archivo ?? 'Nombre no disponible' }}</p>
                <p class="text-sm text-gray-500">{{ ucfirst($documento->tipo_documento) }}</p>
            </div>
            <a href="{{ \Illuminate\Support\Facades\Storage::url($documento->ruta_archivo) }}" target="_blank"
               class="ml-4 inline-flex items-center justify-center px-3 py-1 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 flex-shrink-0">
                Ver Documento
            </a>
        </div>
    @endforeach
</div>
