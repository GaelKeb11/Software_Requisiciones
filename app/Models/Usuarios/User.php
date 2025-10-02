<?php

namespace App\Models\Usuarios;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Recepcion\Departamento;
use App\Models\Usuarios\Rol;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_usuario'; // <-- Agregar clave primaria personalizada

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_departamento', // <-- Nuevo campo
        'id_rol',          // <-- Nuevo campo
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function nombreCompleto(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->name} {$this->apellido_paterno} {$this->apellido_materno}",
        );
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }


    protected $attributes = [
        'id_departamento' => 1, // Puedes poner el valor por defecto que desees
        'id_rol' => 1,          // Puedes poner el valor por defecto que desees
    ];

    public function roles(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }
}
