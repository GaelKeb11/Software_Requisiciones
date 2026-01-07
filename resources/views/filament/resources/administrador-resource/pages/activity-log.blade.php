<x-filament-panels::page>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Bitácora de actividad (últimos 200 registros)
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Datos desde la tabla activity_log con evento, sujeto, usuario y propiedades.
                    </p>
                </div>
            </div>
            <div class="p-4 overflow-x-auto">
                @if(count($logs) > 0)
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 text-left text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="px-3 py-2">ID</th>
                                <th class="px-3 py-2">Log</th>
                                <th class="px-3 py-2">Evento</th>
                                <th class="px-3 py-2">Descripción</th>
                                <th class="px-3 py-2">Sujeto</th>
                                <th class="px-3 py-2">Usuario/Causer</th>
                                <th class="px-3 py-2">Props</th>
                                <th class="px-3 py-2">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-200">
                            @foreach($logs as $log)
                                @php
                                    $props = $log['properties'] ?? null;
                                    if (is_array($props) || is_object($props)) {
                                        $props = json_encode($props, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                    }
                                @endphp
                                <tr class="align-top">
                                    <td class="px-3 py-2 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $log['id'] }}</td>
                                    <td class="px-3 py-2">
                                        <div class="font-semibold">{{ $log['log_name'] ?? 'default' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Batch: {{ $log['batch_uuid'] ?? '—' }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-xs">{{ $log['event'] ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $log['description'] }}</td>
                                    <td class="px-3 py-2 text-xs">
                                        <div>{{ $log['subject_type'] ?? '—' }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">ID: {{ $log['subject_id'] ?? '—' }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        <div>{{ $log['causer_type'] ?? '—' }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">ID: {{ $log['causer_id'] ?? '—' }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        @if($props)
                                            <pre class="whitespace-pre-wrap break-words bg-gray-50 dark:bg-gray-900/50 p-2 rounded border border-gray-100 dark:border-gray-800">{{ $props }}</pre>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">
                                        <div>{{ $log['created_at'] }}</div>
                                        <div class="text-[11px]">Act: {{ $log['updated_at'] }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-500 py-8">
                        No hay registros disponibles.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
