<?php

namespace App\Mail;

use App\Models\Recepcion\Requisicion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequisicionCreada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Requisicion $requisicion)
    {
    }

    public function build(): self
    {
        $solicitante = $this->requisicion->solicitante;
        $departamento = $solicitante?->departamento?->nombre;
        $nombreSolicitante = $solicitante
            ? trim("{$solicitante->name} {$solicitante->apellido_paterno} {$solicitante->apellido_materno}")
            : 'Sin solicitante';

        $detalles = [
            'folio' => $this->requisicion->folio,
            'usuario' => $nombreSolicitante,
            'depto' => $departamento ?? 'Sin departamento',
        ];

        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Nueva Requisicion Generada - Folio #' . $detalles['folio'])
            ->view('emails.requisicion', ['detalles' => $detalles]);
    }
}

