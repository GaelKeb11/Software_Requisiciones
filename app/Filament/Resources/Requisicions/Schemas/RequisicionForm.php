<?php

namespace App\Filament\Resources\Requisicions\Schemas;

use Filament\Schemas\Schema;

class RequisicionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('folio')->required(),
                \Filament\Forms\Components\DatePicker::make('fecha_creacion')->required(),
                \Filament\Forms\Components\DatePicker::make('fecha_recepcion')
                    ->default(now()->toDateString())
                    ->readOnly()
                    ->hidden(),
                \Filament\Forms\Components\TimePicker::make('hora_recepcion')
                    ->default(now()->format('H:i'))
                    ->readOnly()
                    ->hidden(),
                \Filament\Forms\Components\Textarea::make('concepto')->required(),
                \Filament\Forms\Components\Select::make('id_departamento')
                    ->relationship('departamento', 'nombre')
                    ->required(),
                \Filament\Forms\Components\Select::make('id_clasificacion')
                    ->relationship('clasificacion', 'nombre')
                    ->required(),
                \Filament\Forms\Components\Select::make('id_usuario')
                    ->relationship('usuario', 'name'),
                //\Filament\Forms\Components\Select::make('id_estatus')
                   // ->relationship('estatus', 'nombre'),
            ]);
    }
}
