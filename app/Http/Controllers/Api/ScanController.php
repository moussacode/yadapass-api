<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function recent()
    {
        // Récupérer les 5 derniers scans (avec relations si besoin)
        $scans = Scan::orderBy('date_heure', 'desc')
            ->take(5)
            ->get(['id',  'date_heure', 'validation', 'statut_acces', 'commentaire']);

        return response()->json([
            'success' => true,
            'data' => $scans,
        ]);
    }
}
