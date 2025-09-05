<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('recepcion')->group(function () {
    Route::resource('requisiciones', \App\Http\Controllers\Recepcion\RequisicionController::class)
        ->names('recepcion.requisiciones');
});