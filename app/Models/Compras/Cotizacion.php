<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Recepcion\Requisicion;
use App\Models\Usuarios\Usuario;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';
    protected $primaryKey = 'id_cotizacion';

    protected $fillable = [
        'id_requisicion',
        'nombre_proveedor',
        'fecha_cotizacion',
        'total_cotizado',
        'id_usuario_gestor'
    ];

    protected $casts = [
        'fecha_cotizacion' => 'date',
    ];

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion');
    }

    public function usuarioGestor(): BelongsTo
    {
        // Assuming 'users' table and Usuario model are standard
        return $this->belongsTo(Usuario::class, 'id_usuario_gestor');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCotizacion::class, 'id_cotizacion');
    }
}

