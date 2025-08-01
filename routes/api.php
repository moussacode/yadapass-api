<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\CarteEtudianteApiController;
use App\Http\Controllers\CarteEtudianteController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\PaiementController;
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
    Route::post('/valider-scan', [CarteEtudianteApiController::class, 'validerScanUnique']);
    Route::get('/etudiants/{id}/arrieres', [PaiementController::class, 'getArrieres']);
    Route::get('/etudiants/{id}/paiements-mensuels', [PaiementController::class, 'getDetailsMensuels']);
    Route::get('/carte-etudiante/{matricule}/infos-financieres', [PaiementController::class, 'getInfosAvecArrieres']);

    // Ajouter ici d’autres routes sécurisées
    Route::put('/personnel/update-password', [AuthController::class, 'updatePassword']);

    // Exemple pour scanner un étudiant
    Route::get('/scan/{matricule}', [EtudiantController::class, 'showByMatricule']);
});
Route::middleware('auth:sanctum')->post('/change-password', [AuthController::class, 'changePassword']);
Route::middleware('auth:sanctum')->get('/scans/recent', [ScanController::class, 'recent']);
Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne !',
        'timestamp' => now()
    ]);
});