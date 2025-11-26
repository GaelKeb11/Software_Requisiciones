<x-filament-panels::page>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Registros del Sistema (Últimas 100 líneas)
                </h3>
            </div>
            <div class="p-4 overflow-x-auto">
                @if(count($logs) > 0)
                    <div class="font-mono text-sm space-y-1">
                        @foreach($logs as $log)
                            <div class="whitespace-pre-wrap break-words text-gray-600 dark:text-gray-300 border-b border-gray-100 dark:border-gray-800 pb-1 last:border-0">
                                {{ $log }}
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        No hay registros disponibles.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>

