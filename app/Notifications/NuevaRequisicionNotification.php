<?php

namespace App\Notifications;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Recepcion\Requisicion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Notifications\Notification;

class NuevaRequisicionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Requisicion $requisicion)
    {
    }

    /**
     * Definimos los canales de notificación: Correo y Base de Datos (Panel de notificaciones)
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Configuración del correo electrónico
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva requisición registrada')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Se registró una nueva requisición.')
            ->line("Folio: {$this->requisicion->folio}")
            ->line("Concepto: {$this->requisicion->concepto}")
            ->line("Estatus: {$this->requisicion->estatus?->nombre}")
            ->line('Ingresa al sistema para revisarla.')
            ->action('Ver requisición', $this->viewUrl())
            ->line('Gracias.');
    }

    /**
     * Datos que se guardarán en la tabla 'notifications' de la base de datos
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'nueva_requisicion',
            'requisicion_id' => $this->requisicion->getKey(),
            'folio' => $this->requisicion->folio,
            'concepto' => $this->requisicion->concepto,
            'estatus' => $this->requisicion->estatus?->nombre,
            'url' => $this->viewUrl(),
        ];
    }

    

    private function viewUrl(): string
    {
        return RequisicionResource::getUrl('view', ['record' => $this->requisicion]);
    }
}

