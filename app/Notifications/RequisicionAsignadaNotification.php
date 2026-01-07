<?php

namespace App\Notifications;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Recepcion\Requisicion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class RequisicionAsignadaNotification extends Notification implements ShouldQueue
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
            ->subject('Requisición asignada')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Se te asignó una requisición para gestión de compras.')
            ->line("Folio: {$this->requisicion->folio}")
            ->line("Concepto: {$this->requisicion->concepto}")
            ->line('Revisa y continúa con el proceso.')
            ->action('Abrir requisición', $this->viewUrl())
            ->line('Gracias.');
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        $text = sprintf(
            'Asignada requisición %s. Concepto: %s',
            $this->requisicion->folio,
            str($this->requisicion->concepto)->limit(60)
        );

        return (new VonageMessage)->content($text);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'requisicion_asignada',
            'requisicion_id' => $this->requisicion->getKey(),
            'folio' => $this->requisicion->folio,
            'concepto' => $this->requisicion->concepto,
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

