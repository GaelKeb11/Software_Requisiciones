<?php

namespace App\Http\Resources\Recepcion;

use Illuminate\Http\Resources\Json\JsonResource;

class RequisicionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_requisicion' => $this->id_requisicion,
            'folio' => $this->folio,
            'fecha_creacion' => $this->fecha_creacion->format('Y-m-d'),
            'fecha_recepcion' => $this->fecha_recepcion->format('Y-m-d'),
            'hora_recepcion' => $this->hora_recepcion,
            'concepto' => $this->concepto,
            'departamento' => $this->departamento->nombre,
            'clasificacion' => $this->clasificacion->nombre,
            'usuario' => $this->usuario->name,
            'estatus' => $this->estatus->nombre,
            'documentos' => $this->documentos
        ];
    }
}