<?php

namespace App\Filament\Resources\Solicitudes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class FormularioSolicitud
{
    public static function configure(Schema $form): Schema
    {
        $user = Auth::user();

        return $form->schema([
            Tabs::make('Crear Solicitud')
                ->tabs([
                    Tab::make('Información General')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            TextInput::make('solicitante')
                                ->label('Solicitante')
                                ->default($user->name)
                                ->disabled(),
                            DatePicker::make('fecha_creacion')
                                ->label('Fecha de Solicitud')
                                ->default(now())
                                ->disabled()
                                ->dehydrated(true),
                            Textarea::make('concepto')
                                ->label('Concepto General')
                                ->required()
                                ->minLength(10) // Validación de longitud mínima
                                ->maxLength(500) // Validación de longitud máxima
                                ->columnSpanFull(),
                            Select::make('id_clasificacion')
                                ->label('Clasificación')
                                ->relationship('clasificacion', 'nombre')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('id_estatus')
                                ->hidden(fn() => true)
                                ->dehydrated(true)
                                ->default(1),
                        ])
                        ->columns(3),

                    Tab::make('Artículos Solicitados')
                        ->icon('heroicon-o-shopping-cart')
                        ->schema([
                            Repeater::make('detalles')
                                ->relationship()
                                ->label('')
                                ->schema([
                                    TextInput::make('cantidad')
                                        ->label('Cantidad')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1) // Validación de valor mínimo
                                        ->default(1),
                                    Select::make('unidad_medida')
                                        ->label('Unidad (Pza, Caja, etc.)')
                                        ->options([
                                            'Materiales	' => 'Materiales',
                                            'Servicios' => 'Servicios',
                                            'Equipos' => 'Equipos',
                                            'Otro' => 'Otros',
                                        ])
                                        ->required(),
                                    TextInput::make('descripcion')
                                        ->label('Descripción del Artículo')
                                        ->required()
                                        ->minLength(5) // Validación de longitud mínima
                                        ->maxLength(255) // Validación de longitud máxima
                                        ->columnSpan(2),
                                    TextInput::make('total')
                                        ->label('Total')
                                        ->numeric()
                                        ->default(0)
                                        ->disabled(),
                                ])
                                ->addActionLabel('+ Añadir Artículo')
                                ->columns(4)
                                ->defaultItems(1)
                                ->required()
                                ->collapsible(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
