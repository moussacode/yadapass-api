<?php

namespace App\Observers;

use App\Models\Attribution;
use App\Models\CarteEtudiante;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttributionObserver
{
    public function created(Attribution $attribution): void
    {
        try {
            $etudiant = $attribution->etudiant;

            if (!$etudiant || empty($etudiant->matricule)) {
                Log::warning("❗ Attribution {$attribution->id} sans étudiant valide.");
                return;
            }

            $qrText = strtoupper($etudiant->matricule);
            $folder = 'qr_codes';
            $filename = "$folder/qr_{$attribution->id}.svg";

            // Assurez-vous que le dossier existe
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
                Log::info("📁 Dossier créé : $folder");
            }

            // Générer le QR code SVG
            $qrSvg = QrCode::format('svg')->size(200)->generate($qrText);

            // Sauvegarder le QR code
            Storage::disk('public')->put($filename, $qrSvg);
            Log::info("✅ QR SVG sauvegardé : $filename");

            // Créer la carte étudiante
            CarteEtudiante::create([
                'attribution_id' => $attribution->id,
                'qr_code' => $qrText,
                'qr_data' => $filename, // chemin du fichier
                'statut' => 'active',
                'date_emission' => now(),
            ]);

            Log::info("✅ Carte Étudiante créée pour attribution {$attribution->id}");

        } catch (\Exception $e) {
            Log::error("❌ Erreur AttributionObserver@created : " . $e->getMessage());
        }
    }

    public function updated(Attribution $attribution): void {}
    public function deleted(Attribution $attribution): void {}
    public function restored(Attribution $attribution): void {}
    public function forceDeleted(Attribution $attribution): void {}
}
