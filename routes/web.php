<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadFileController;


Route::get('/', function () {
    return view('welcome');
});

Route::prefix('recepcion')->group(function () {
    Route::resource('requisiciones', \App\Http\Controllers\Recepcion\RequisicionController::class)
        ->names('recepcion.requisiciones');
});

Route::get('download/{file}', [\App\Http\Controllers\DownloadFileController::class, '__invoke'])
    ->name('download.file');