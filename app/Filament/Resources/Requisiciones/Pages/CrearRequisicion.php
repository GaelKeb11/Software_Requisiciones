<?php

namespace App\Filament\Resources\Requisicions\Pages;

use App\Filament\Resources\Requisicions\RequisicionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRequisicion extends CreateRecord
{
    protected static string $resource = RequisicionResource::class;

    protected function afterCreate(): void
    {
        $requisicion = $this->record;
        $data = $this->form->getState();

        if (!empty($data['documentos'])) {
            foreach ($data['documentos'] as $fileName) {
                $requisicion->documentos()->create([
                    'tipo_documento' => 'general',
                    'nombre_archivo' => $fileName, // nombre original
                    'ruta_archivo' => 'documentos/' . $fileName, // ruta relativa en storage
                    'comentarios' => null,
                ]);
            }
        }
    }
}
