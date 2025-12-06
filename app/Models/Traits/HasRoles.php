<?php

namespace App\Models\Traits;

use App\Enums\RolEnum;

trait HasRoles
{
    /**
     * Verifica si el usuario tiene el rol de Administrador.
     */
    public function esAdministrador(): bool
    {
        return $this->rol?->nombre === RolEnum::ADMINISTRADOR->value;
    }

    /**
     * Verifica si el usuario tiene el rol de Recepcionista.
     */
    public function esRecepcionista(): bool
    {
        return $this->rol?->nombre === RolEnum::RECEPCIONISTA->value;
    }

    /**
     * Verifica si el usuario tiene el rol de Gestor de Compras.
     */
    public function esGestorDeCompras(): bool
    {
        return $this->rol?->nombre === RolEnum::GESTOR_COMPRAS->value;
    }

    /**
     * Verifica si el usuario tiene el rol de Solicitante.
     */
    public function esSolicitante(): bool
    {
        return $this->rol?->nombre === RolEnum::SOLICITANTE->value;
    }

    /**
     * Verifica si el usuario tiene el rol de Director.
     */
    public function esDirector(): bool
    {
        return $this->rol?->nombre === RolEnum::DIRECTOR->value;
    }

    /**
     * Verifica si el usuario tiene alguno de los roles de acceso al panel.
     */
    public function puedeAccederAlPanel(): bool
    {
        return $this->esAdministrador() || $this->esRecepcionista() || $this->esGestorDeCompras() || $this->esSolicitante();
    }
}
