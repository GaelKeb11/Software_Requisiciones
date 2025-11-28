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
use Filament\Notifications\Notification;

class AsignarRequisicion extends EditRecord
{
    protected static string $resource = RequisicionResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if ($this->record->id_usuario !== null) {
            Notification::make()
                ->title('Acceso denegado')
                ->body('Esta requisición ya tiene un usuario asignado y no puede ser modificada.')
                ->danger()
                ->send();

            $this->redirect(RequisicionResource::getUrl('index'));
            return;
        }

        parent::mount($record);
    }

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
        $data['id_estatus'] = 3; // Asignada / En Cotización (id_estatus = 3 según instrucciones)
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return RequisicionResource::getUrl('index');
    }
}
