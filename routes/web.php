<?php

use App\Http\Controllers\CarteEtudianteController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', 'admin/login');

Route::middleware([ 'auth'])->prefix('carte-etudiante')->name('carte.')->group(function () {
    Route::get('preview/{carte}', [CarteEtudianteController::class, 'preview'])->name('preview');
    Route::get('print/{carte}', [CarteEtudianteController::class, 'print'])->name('print');
    Route::get('bulk-print', [CarteEtudianteController::class, 'bulkPrint'])->name('bulk-print');
});