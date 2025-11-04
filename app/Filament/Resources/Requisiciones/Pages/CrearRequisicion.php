<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use Filament\Resources\Pages\CreateRecord;

class CrearRequisicion extends CreateRecord
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
