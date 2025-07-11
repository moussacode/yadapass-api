<?php

use App\Http\Controllers\CarteEtudianteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarteEtudianteApiController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', 'admin/login');

Route::middleware([ 'auth'])->prefix('carte-etudiante')->name('carte.')->group(function () {
    Route::get('preview/{carte}', [CarteEtudianteController::class, 'preview'])->name('preview');
    Route::get('print/{carte}', [CarteEtudianteController::class, 'print'])->name('print');
    Route::get('bulk-print', [CarteEtudianteController::class, 'bulkPrint'])->name('bulk-print');
});


Route::get('/test-csrf', function () {
    return csrf_token();
});