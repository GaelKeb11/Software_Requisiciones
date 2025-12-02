<?php

namespace App\Enums;

enum RolEnum: string
{
    case ADMINISTRADOR = 'Administrador';
    case RECEPCIONISTA = 'Recepcionista';
    case GESTOR_COMPRAS = 'Gestor de Compras';
    case SOLICITANTE = 'Solicitante';
    case DIRECTOR = 'Director';
    case TESORERIA = 'Tesorería';
}
