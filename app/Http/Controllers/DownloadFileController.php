<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadFileController extends Controller
{
    public function __invoke($file)
    {
        $disk = Storage::disk('public');
        $filePath = $file; // This should be the relative path in the disk, e.g., 'documentos/filename.ext'

        if (!$disk->exists($filePath)) {
            abort(404, 'Archivo no disponible');
        }
        
        return response()->download(
            $disk->path($filePath),
            basename($filePath), // Nombre del archivo en la descarga
            ['Content-Type' => 'application/octet-stream'] // Fuerza descarga
        );
    }
}