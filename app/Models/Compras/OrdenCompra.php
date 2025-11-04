<?php

namespace App\Models\Compras;

use App\Models\Recepcion\Requisicion;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compra';
    protected $primaryKey = 'id_orden_compra';

    protected $fillable = [
        'id_requisicion',
        'nombre_proveedor',
        'fecha_orden',
        'total_calculado',
        'id_usuario_gestor',
    ];

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion', 'id_requisicion');
    }

    public function gestor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_gestor', 'id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleOrdenCompra::class, 'id_orden_compra', 'id_orden_compra');
    }
}
