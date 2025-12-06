<?php

namespace App\Filament\Resources\Compras\GestionCompras\Pages;

use App\Filament\Resources\Compras\GestionCompras\GestionComprasResource;
use App\Models\Compras\Cotizacion;
use App\Models\Compras\DetalleCotizacion;
use App\Models\Recepcion\Requisicion;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class EditGestionCompras extends EditRecord
{
    protected static string $resource = GestionComprasResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if (! in_array($this->record->id_estatus, [3, 5])) {
            Notification::make()
                ->title('No editable')
                ->body('Esta requisición no se puede modificar en este momento.')
                ->warning()
                ->send();
            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }

        // Lógica de Inicialización de Cotización
        // Si está en estatus "Asignada / En Cotización" (3) y no tiene cotización, la creamos.
        if ($this->record->id_estatus == 3 && !$this->record->cotizaciones()->exists()) {
            
            $cotizacion = Cotizacion::create([
                'id_requisicion' => $this->record->id_requisicion,
                'id_usuario_gestor' => Auth::id() ?? 1, // Fallback para dev
                'fecha_cotizacion' => now(),
                'nombre_proveedor' => '', // Para que el usuario lo llene
                'total_cotizado' => 0
            ]);

            // Caso 1: Si tiene detalles de requisición, precargamos los detalles de cotización
            if ($this->record->detalles()->exists()) {
                foreach ($this->record->detalles as $detalleReq) {
                    DetalleCotizacion::create([
                        'id_cotizacion' => $cotizacion->id_cotizacion,
                        'id_detalle_requisicion' => $detalleReq->id_detalle_requisicion,
                        'cantidad_cotizada' => $detalleReq->cantidad,
                        'unidad_medida' => $detalleReq->unidad_medida,
                        'descripcion' => $detalleReq->descripcion,
                        'precio_unitario' => 0,
                        'subtotal' => 0
                    ]);
                }
            }
        }

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enviar_aprobacion')
                ->label('Enviar para Aprobación')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->action(function () {
                    $this->save(); // Guardar cambios primero
                    
                    // Validaciones básicas
                    // Si es Caso 1: Verificar totales > 0
                    if ($this->record->detalles()->exists()) {
                        $cotizacion = $this->record->cotizaciones()->first();
                        if ($cotizacion && $cotizacion->detalles()->sum('subtotal') <= 0) {
                             Notification::make()
                                ->title('Error')
                                ->body('Debe ingresar precios para los ítems.')
                                ->danger()
                                ->send();
                            return;
                        }
                    }
                    // Si es Caso 2: Verificar documento? (El campo es required en el form, pero validamos aquí también si se quiere)
                    
                    // Transición de Estatus
                    $this->record->update(['id_estatus' => 4]); // 4 = Pendiente de Aprobación

                    Notification::make()
                        ->title('Requisición enviada para aprobación')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->id_estatus == 3), // Solo visible si está en cotización
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
