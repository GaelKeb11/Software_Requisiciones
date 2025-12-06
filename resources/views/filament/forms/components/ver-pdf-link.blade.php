<div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
    @php
        $record = $getRecord();
        $documento = $record->documentos()->where('tipo_documento', 'Requisición')->first();
    @endphp

    @if ($documento)
        <div class="flex items-center gap-2 p-2 mb-2 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            <x-heroicon-o-document-text class="w-2 h-2" />
            <span class="font-medium">Documento de Requisición:</span>
            <a href="{{ Storage::url($documento->ruta_archivo) }}" 
               target="_blank" 
               class="font-semibold underline hover:text-blue-900">
               Ver PDF Original
            </a>
        </div>
    @endif
</div>
