<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadFileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('recepcion')->group(function () {
    Route::resource('requisiciones', \App\Http\Controllers\Recepcion\RequisicionController::class)
        ->names('recepcion.requisiciones');
});

Route::get('download/{file}', [\App\Http\Controllers\DownloadFileController::class, '__invoke'])
    ->name('download.file');

Route::get('/update-user-password', function () {
    $email = 'admin@admin.com'; // <-- CAMBIA ESTE EMAIL por el del usuario que quieras actualizar
    $newPassword = 'password'; // <-- CAMBIA ESTA CONTRASEÑA por la nueva contraseña

    $user = \App\Models\Usuarios\Usuario::where('email', $email)->first();

    if ($user) {
        $user->password = Illuminate\Support\Facades\Hash::make($newPassword);
        $user->save();
        return "Contraseña para el usuario {$email} actualizada correctamente.";
    }

    return "Usuario con email {$email} no encontrado.";
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('logs', [LogController::class, 'index']);
});