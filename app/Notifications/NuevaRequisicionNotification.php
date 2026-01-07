<?php

namespace App\Notifications;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Recepcion\Requisicion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class NuevaRequisicionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Requisicion $requisicion)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];

        if ($this->shouldSendSms($notifiable)) {
            $channels[] = 'vonage';
        }

        return $channels;
    }

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

    public function toVonage(object $notifiable): VonageMessage
    {
        $text = sprintf(
            'Nueva requisición %s - %s',
            $this->requisicion->folio,
            str($this->requisicion->concepto)->limit(60)
        );

        return (new VonageMessage)->content($text);
    }

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

    private function shouldSendSms(object $notifiable): bool
    {
        return filled($notifiable->numero_telefonico) && filled(config('services.vonage.key'));
    }

    private function viewUrl(): string
    {
        return RequisicionResource::getUrl('view', ['record' => $this->requisicion]);
    }
}

