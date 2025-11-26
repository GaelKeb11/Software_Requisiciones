<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    public function index()
    {
        $archivos = Storage::files('logs');
        $logs = [];
        
        foreach ($archivos as $archivo) {
            $logs[] = [
                'nombre' => basename($archivo),
                'contenido' => Storage::get($archivo)
            ];
        }
        
        return view('admin.logs', compact('logs'));
    }
}
