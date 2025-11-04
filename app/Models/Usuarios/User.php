<?php

namespace App\Models\Usuarios;


use App\Models\Recepcion\Departamento;
use App\Models\Usuarios\Rol;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar; // Se importa el contrato del avatar
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Support\Facades\URL;

// CORRECCIÓN: 'HasAvatar' se añade a la lista de 'implements'.
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAvatar
{
    // La línea 'use HasAvatar;' ha sido eliminada de aquí, ya que no es un Trait.
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'name', 'email', 'password', 'id_departamento', 'id_rol',
        'apellido_paterno', 'apellido_materno', 'numero_telefonico',
        'profile_photo_path',
    ];


    protected $hidden = [
        'password', 'remember_token', 'app_authentication_secret',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'app_authentication_secret' => 'encrypted',
        ];
    }
    
    // ===============================================
    // RELACIONES DE LA APLICACIÓN
    // ===============================================
    
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    protected $attributes = [
        'id_departamento' => null,
        'id_rol' => null,
    ];

    // ===============================================
    // ATRIBUTOS PERSONALIZADOS
    // ===============================================

    protected function nombreCompleto(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->name} {$this->apellido_paterno} {$this->apellido_materno}"),
        );
    }
    
    // ===============================================
    // MÉTODOS DE AUTORIZACIÓN DE FILAMENT
    // ===============================================

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->rol && in_array($this->rol->nombre, ['Administrador', 'Recepcionista', 'Gestor de Compras','Solicitante']);
    }

    public function hasRole(string $role): bool
    {
        return $this->rol->nombre === $role;
    }

    // ===============================================
    // MÉTODOS REQUERIDOS POR HasAppAuthentication
    // ===============================================

    public function getAppAuthenticationSecret(): ?string
    {
        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }

    // ===============================================
    // MÉTODO REQUERIDO POR LA INTERFAZ HasAvatar
    // ===============================================

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->profile_photo_path) {
            return URL::to(Storage::url($this->profile_photo_path));
        }

        // Genera un avatar por defecto con las iniciales del usuario
        $nombreCompleto = trim("{$this->name} {$this->apellido_paterno} {$this->apellido_materno}");
        return 'https://ui-avatars.com/api/?name=' . urlencode($nombreCompleto) . '&color=7F9CF5&background=EBF4FF';
    }
}
