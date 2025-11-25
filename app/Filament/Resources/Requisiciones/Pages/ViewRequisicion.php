<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class ViewRequisicion extends ViewRecord
{
    protected static string $resource = RequisicionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información General')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('folio'),
                        TextEntry::make('fecha_creacion')->date(),
                        TextEntry::make('departamento.nombre')->label('Dependencia'),
                        TextEntry::make('clasificacion.nombre')->label('Clasificación'),
                        TextEntry::make('estatus.nombre')->label('Estatus')->badge()->color(fn (string $state): string => match ($state) {
                            'Recepcionada' => 'gray',
                            'Pendientes' => 'warning',
                            'Asignada' => 'info',
                            'En Cotización' => 'info',
                            'En Revisión' => 'primary',
                            'Rechazada' => 'danger',
                            'Aprobada' => 'success',
                            'Completada' => 'success',
                            default => 'secondary',
                        }),
                        TextEntry::make('usuario.name')->label('Asignado a'),
                        TextEntry::make('concepto')->columnSpanFull(),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Actualizar solo la requisición principal
        $record->update($data);
        
        return $record;
    }
}
