<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQrCodeTest extends Command
{
    protected $signature = 'qr:generate-test';
    protected $description = 'Génère un QR code SVG pour test';

    public function handle()
    {
        $matricule = 'ETU20250001';
        $fileName = 'qr_' . $matricule . '.svg';
        $folder = 'qr_codes';

        // Créer le dossier s’il n’existe pas
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
            $this->info("📁 Dossier créé : $folder");
        }

        // Générer le QR code au format SVG
        $qrSvg = QrCode::format('svg')->size(200)->generate($matricule);
        Storage::disk('public')->put("$folder/$fileName", $qrSvg);

        $this->info("✅ QR code SVG généré : storage/app/public/$folder/$fileName");

        return Command::SUCCESS;
    }
}
