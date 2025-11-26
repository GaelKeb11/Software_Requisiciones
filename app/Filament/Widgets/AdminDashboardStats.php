<?php

namespace App\Filament\Widgets;

use App\Models\HistorialLogin;
use App\Models\IntentoFallidoLogin;
use App\Models\Recepcion\Requisicion;
use App\Models\Usuarios\Usuario;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AdminDashboardStats extends BaseWidget
{
    public static function canView(): bool
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();
        return $user->esAdministrador();
    }

    protected function getStats(): array
    {
        // Verificar usuarios activos basados en sesiones
        $usuariosEnLinea = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(5)->getTimestamp())
            ->count();

        return [
            Stat::make('Usuarios en línea', $usuariosEnLinea)
                ->description('Activos en los últimos 5 minutos')
                ->icon('heroicon-o-user-group')
                ->color('success'),
                
            Stat::make('Total de usuarios', Usuario::count())
                ->icon('heroicon-o-users')
                ->color('primary'),
                
            Stat::make('Inicios de sesión (hoy)', HistorialLogin::whereDate('created_at', today())->count())
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('info'),
                
            Stat::make('Requisiciones pendientes', Requisicion::whereHas('estatus', function ($query) {
                $query->where('nombre', 'Pendiente'); // O el nombre exacto que consideres "pendiente"
            })->count())
                ->icon('heroicon-o-document-text')
                ->color('warning'),
                
            Stat::make('Intentos fallidos (hoy)', IntentoFallidoLogin::whereDate('created_at', today())->count())
                ->icon('heroicon-o-shield-exclamation')
                ->color('danger'),
        ];
    }
}
