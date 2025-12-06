<?php

namespace App\Http\Controllers\Admin;

use App\Models\HistorialLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Usuarios\Usuario;
use Illuminate\Support\Facades\DB;
use App\Models\Recepcion\Requisicion;

class DashboardController extends Controller
{
    public function index()
    {
        $logins = HistorialLogin::selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->get();

        $usuariosEnLinea = Usuario::where('ultima_actividad', '>', now()->subMinutes(5))->count();
        $totalUsuarios = Usuario::count();
        
        $requisiciones = Requisicion::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get();

        return view('admin.dashboard', compact(
            'logins',
            'usuariosEnLinea',
            'totalUsuarios',
            'requisiciones'
        ));
    }
}
