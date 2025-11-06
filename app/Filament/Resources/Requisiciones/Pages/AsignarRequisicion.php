<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use App\Models\Recepcion\Requisicion;
use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Usuarios\Usuario;

class AsignarRequisicion extends EditRecord
{
    protected static string $resource = RequisicionResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('folio')
                    ->label('Folio')
                    ->disabled(),
                DatePicker::make('fecha_creacion')
                    ->label('Fecha de Creación')
                    ->disabled(),
                DatePicker::make('fecha_recepcion')
                    ->label('Fecha de Recepción')
                    ->disabled(),
                TextInput::make('hora_recepcion')
                    ->label('Hora de Recepción')
                    ->disabled(),
                Textarea::make('concepto')
                    ->label('Concepto')
                    ->disabled(),
                Select::make('id_usuario')
                    ->relationship('usuario', 'name', function ($query) {
                        return $query->whereHas('rol', function ($query) {
                            $query->where('nombre', 'Gestor de Compras');
                        });
                    })
                    ->label('Asignado a')
                    ->required()
                    ->searchable(),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['id_estatus'] = 4; // En Cotización
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return RequisicionResource::getUrl('index');
    }
}
