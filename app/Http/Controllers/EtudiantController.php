<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class EtudiantController extends Controller
{
   

public function showByMatricule($matricule)
{
    Log::info("Recherche étudiant avec matricule : $matricule");

    $etudiant = Etudiant::where('matricule', $matricule)->first();

    if (!$etudiant) {
        Log::warning("Étudiant non trouvé : $matricule");
        return response()->json([
            'success' => false,
            'message' => 'Étudiant non trouvé.',
        ], 404);
    }

    Log::info("Étudiant trouvé : ID {$etudiant->id}");

    return response()->json([
        'success' => true,
        'etudiant' => $etudiant
    ]);
}

}
