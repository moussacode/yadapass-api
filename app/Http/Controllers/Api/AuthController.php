<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonnelSecurite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $personnel = PersonnelSecurite::where('email', $request->email)->first();

        if (!$personnel || !Hash::check($request->password, $personnel->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations de connexion sont incorrectes.'],
            ]);
        }

        // Supprimer les anciens tokens
        $personnel->tokens()->delete();

        // Créer un nouveau token
        $token = $personnel->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => [
                    'id' => $personnel->id,
                    'nom' => $personnel->nom,
                    'prenom' => $personnel->prenom,
                    'email' => $personnel->email,
                    'poste' => $personnel->poste,
                    'phone' => $personnel->phone,
                    'full_name' => $personnel->full_name,
                ],
                'token' => $token,
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'poste' => $user->poste,
                    'phone' => $user->phone,
                    'full_name' => $user->full_name,
                ]
            ]
        ], 200);
    }
    public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = $request->user();

    // Vérifie si l'ancien mot de passe est correct
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Le mot de passe actuel est incorrect.',
        ], 422);
    }

    // Met à jour le mot de passe
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Mot de passe mis à jour avec succès.',
    ]);
}

}