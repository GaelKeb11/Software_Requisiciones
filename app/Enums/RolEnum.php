<?php

namespace App\Enums;

enum RolEnum: string
{
    case ADMINISTRADOR = 'Administrador';
    case RECEPCIONISTA = 'Recepcionista';
    case GESTOR_ADMINISTRACION = 'Gestor de Administración';
    case SOLICITANTE = 'Solicitante';
    case DIRECTOR = 'Director';
    case TESORERIA = 'Tesorería';
}
