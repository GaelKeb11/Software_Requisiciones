<?php

namespace App\Observers;

use App\Models\Recepcion\Requisicion;
use Illuminate\Support\Facades\Auth;

class RequisicionObserver
{
    /**
     * Handle the Requisicion "created" event.
     */
    public function creating(Requisicion $requisicion): void
    {
        // 1. Obtener el usuario autenticado y su departamento.
        $user = Auth::user();
        
        // Asegurarse de que el usuario y su departamento existen.
        if (!$user || !$user->departamento) {
            // Considera lanzar una excepción o manejar el caso donde el usuario no tiene departamento.
            // Por ahora, se usará un prefijo por defecto y no se filtrará por departamento.
            $prefijo = 'GEN';
            $departamentoId = null;
        } else {
            $prefijo = $user->departamento->prefijo ?? 'GEN';
            $departamentoId = $user->departamento->id_departamento;
        }

        // 2. Obtener el año actual.
        $year = date('Y');

        // 3. Buscar la última requisición del departamento en el año actual.
        $query = Requisicion::whereYear('fecha_creacion', $year);

        if ($departamentoId) {
            $query->where('id_departamento', $departamentoId);
        }

        $lastRequisicion = $query->latest('id_requisicion')->first();
        
        $consecutivo = 1;
        if ($lastRequisicion && $lastRequisicion->folio) {
            // Extraer el último número del folio y sumarle 1.
            $parts = explode('-', $lastRequisicion->folio);
            $lastConsecutivo = (int) end($parts);
            $consecutivo = $lastConsecutivo + 1;
        }

        // 4. Formatear el número con ceros a la izquierda.
        $numeroFormateado = str_pad($consecutivo, 4, '0', STR_PAD_LEFT);

        // 5. Asignar el nuevo folio al modelo.
        $requisicion->folio = "{$prefijo}-{$year}-{$numeroFormateado}";
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
