<?php

namespace App\Observers;

use App\Models\Recepcion\Requisicion;
use Illuminate\Support\Facades\Auth;
use App\Models\Recepcion\Departamento;
use App\Models\Usuarios\Usuario;

class RequisicionObservador
{
    /**
     * Handle the Requisicion "created" event.
     */
    public function creating(Requisicion $requisicion): void
    {
        $user = Auth::user();

        // Si la requisición la captura un recepcionista y ya proporcionó el folio completo,
        // respetamos el valor ingresado (solo normalizamos el formato).
        if ($user && $user->esRecepcionista() && filled($requisicion->folio)) {
            $requisicion->folio = strtoupper(trim($requisicion->folio));
            return;
        }

        $requisicion->folio = $this->generarFolio($requisicion->id_departamento, $user);
    }

    protected function generarFolio(?int $departamentoId, ?Usuario $user): string
    {
        $year = date('Y');
        $prefijo = 'GEN';

        $departamento = null;
        if ($departamentoId) {
            $departamento = Departamento::find($departamentoId);
        } elseif ($user && $user->departamento) {
            $departamento = $user->departamento;
        }

        if ($departamento) {
            $prefijo = $departamento->prefijo ?? 'GEN';
            $departamentoId = $departamento->id_departamento;
        } else {
            $departamentoId = null;
        }

        $query = Requisicion::whereYear('fecha_creacion', $year);

        if ($departamentoId) {
            $query->where('id_departamento', $departamentoId);
        }

        $lastRequisicion = $query->latest('id_requisicion')->first();

        $consecutivo = 1;
        if ($lastRequisicion && $lastRequisicion->folio) {
            $parts = explode('-', $lastRequisicion->folio);
            $lastConsecutivo = (int) end($parts);
            $consecutivo = $lastConsecutivo + 1;
        }

        $numeroFormateado = str_pad($consecutivo, 4, '0', STR_PAD_LEFT);

        return "{$prefijo}-{$year}-{$numeroFormateado}";
    }

    /**
     * Handle the Requisicion "updated" event.
     */
    public function updated(Requisicion $requisicion): void
    {
        //
    }

    /**
     * Handle the Requisicion "deleted" event.
     */
    public function deleted(Requisicion $requisicion): void
    {
        //
    }

    /**
     * Handle the Requisicion "restored" event.
     */
    public function restored(Requisicion $requisicion): void
    {
        //
    }

    /**
     * Handle the Requisicion "force deleted" event.
     */
    public function forceDeleted(Requisicion $requisicion): void
    {
        //
    }
}
