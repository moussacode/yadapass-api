<?php

namespace App\Filament\Resources\EtudiantResource\Pages;

use App\Filament\Resources\EtudiantResource;
use App\Models\Etudiant;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CreateEtudiant extends CreateRecord
{
    protected static string $resource = EtudiantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return DB::transaction(function () use ($data) {
            try {
                // ✅ Étape 1 : Validation des données
                $this->validateData($data);

                // ✅ Étape 2 : Générer un matricule si vide
                if (empty($data['matricule'])) {
                    $data['matricule'] = $this->generateMatricule();
                }

                // ✅ Étape 3 : Génération d'un mot de passe si besoin
                // (si tu veux stocker un mot de passe temporaire dans un champ 'password' par exemple)
                // $data['password'] = Hash::make($this->generatePassword());

                // ✅ Étape 4 : Notification simple
                Notification::make()
                    ->title('Étudiant créé avec succès')
                    ->body("Matricule: {$data['matricule']}")
                    ->success()
                    ->send();

                return $data;

            } catch (\Exception $e) {
                Notification::make()
                    ->title('Erreur lors de la création')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();

                throw $e;
            }
        });
    }

    /**
     * Validation personnalisée
     */
    private function validateData(array $data): void
    {
        // Vérifier que le matricule est unique si fourni
        if (!empty($data['matricule']) && Etudiant::where('matricule', $data['matricule'])->exists()) {
            throw new \Exception("Un étudiant avec ce matricule existe déjà");
        }

        // Vérification de l'âge minimum (16 ans)
        if (!empty($data['date_naissance'])) {
            $birthDate = new \DateTime($data['date_naissance']);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;

            if ($age < 16) {
                throw new \Exception("L'étudiant doit avoir au moins 16 ans");
            }
        }
    }

    /**
     * Génère un matricule unique (ex : ETU20250001)
     */
    private function generateMatricule(): string
    {
        $year = date('Y');
        $prefix = 'ETU';

        $lastMatricule = Etudiant::where('matricule', 'like', $prefix . $year . '%')
            ->orderBy('matricule', 'desc')
            ->first();

        $newNumber = $lastMatricule
            ? ((int) substr($lastMatricule->matricule, -4)) + 1
            : 1;

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Génère un mot de passe aléatoire de 8 caractères
     */
    private function generatePassword(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';

        for ($i = 0; $i < 8; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $password;
    }
}
