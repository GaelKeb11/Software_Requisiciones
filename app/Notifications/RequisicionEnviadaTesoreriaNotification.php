<?php

namespace App\Notifications;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Recepcion\Requisicion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class RequisicionEnviadaTesoreriaNotification extends Notification implements ShouldQueue
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
            ->subject('Requisición enviada a Tesorería')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Se envió una requisición para revisión/aprobación de Tesorería.')
            ->line("Folio: {$this->requisicion->folio}")
            ->line("Concepto: {$this->requisicion->concepto}")
            ->line('Ingresa al sistema para atenderla.')
            ->action('Abrir requisición', $this->viewUrl())
            ->line('Gracias.');
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        $text = sprintf(
            'Tesorería: nueva requisición %s (%s)',
            $this->requisicion->folio,
            str($this->requisicion->concepto)->limit(60)
        );

        return (new VonageMessage)->content($text);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'requisicion_tesoreria',
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

