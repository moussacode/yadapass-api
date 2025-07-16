<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CarteEtudianteApiController;
use App\Http\Controllers\CarteEtudianteController;
use App\Http\Controllers\EtudiantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Routes publiques
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées par Sanctum (token obligatoire)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/carte/{matricule}', [CarteEtudianteApiController::class, 'getInfos']);
    Route::get('/carte/{matricule}/emploi', [CarteEtudianteApiController::class, 'getEmploiDuTemps']);

    // Ajouter ici d’autres routes sécurisées
    Route::put('/personnel/update-password', [AuthController::class, 'updatePassword']);

    // Exemple pour scanner un étudiant
    Route::get('/scan/{matricule}', [EtudiantController::class, 'showByMatricule']);
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne !',
        'timestamp' => now()
    ]);
});