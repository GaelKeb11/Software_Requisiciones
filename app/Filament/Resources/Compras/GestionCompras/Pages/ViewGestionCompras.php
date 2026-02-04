<?php

namespace App\Filament\Resources\Compras\GestionCompras\Pages;

use App\Filament\Resources\Compras\GestionCompras\GestionComprasResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ViewGestionCompras extends ViewRecord
{
    protected static string $resource = GestionComprasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ver_documentos')
                ->label('Ver documentos')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->modalHeading('Documentos de la requisición')
                ->modalWidth('3xl')
                ->modalContent(fn () => $this->renderDocumentsModal()),
        ];
    }

    protected function renderDocumentsModal(): HtmlString
    {
        $documentos = $this->record->documentos()->latest()->get();

        if ($documentos->isEmpty()) {
            return new HtmlString('<p class="text-sm text-gray-500">No hay documentos vinculados a esta requisición.</p>');
        }

        $content = $documentos->map(function ($doc) {
            $url = Storage::url($doc->ruta_archivo);

            $iframe = str_contains($doc->ruta_archivo, '.pdf')
                ? "<div class=\"w-full max-w-full\"><iframe src=\"{$url}\" class=\"w-full rounded border\" style=\"min-height: 300px; height: 50vh; max-height: 80vh;\"></iframe></div>"
                : "<div class=\"flex justify-center items-center w-full h-80 bg-gray-50 rounded border\">
                        <p class=\"text-sm text-gray-500\">Vista previa no disponible, utilice la descarga.</p>
                   </div>";

            return "
                <div class=\"space-y-3\">
                    <div class=\"flex items-center justify-between\">
                        <div>
                            <p class=\"text-sm font-semibold\">{$doc->nombre_archivo}</p>
                            <p class=\"text-xs text-gray-500\">Tipo: {$doc->tipo_documento} | Subido el {$doc->created_at?->format('d/m/Y H:i')}</p>
                        </div>
                        <a href=\"{$url}\" target=\"_blank\" class=\"text-sm text-primary-600 hover:underline\">Descargar</a>
                    </div>
                    {$iframe}
                </div>
            ";
        })->join('<hr class="my-6">');

        return new HtmlString("<div class=\"space-y-6\">{$content}</div>");
    }
}

