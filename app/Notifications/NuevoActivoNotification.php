<?php

namespace App\Notifications;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Recepcion\Requisicion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoActivoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Requisicion $requisicion)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo activo generado')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Tu requisición ha sido entregada y se marcó como activo.')
            ->line("Folio: {$this->requisicion->folio}")
            ->line("Concepto: {$this->requisicion->concepto}")
            ->line('Registra el alta del activo en tu sistema correspondiente.')
            ->action('Ver requisición', $this->viewUrl())
            ->line('Gracias.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'nuevo_activo',
            'requisicion_id' => $this->requisicion->getKey(),
            'folio' => $this->requisicion->folio,
            'concepto' => $this->requisicion->concepto,
            'url' => $this->viewUrl(),
        ];
    }

    private function viewUrl(): string
    {
        return RequisicionResource::getUrl('view', ['record' => $this->requisicion]);
    }
}
