<?php

namespace App\Http\Controllers;

use App\Models\CarteEtudiante;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CarteEtudianteController extends Controller
{
    /**
     * Afficher la prévisualisation de la carte
     */
    public function preview(CarteEtudiante $carte)
    {
        $carteInfos = $carte->getCarteInfos();
        
        return view('carte.preview', compact('carte', 'carteInfos'));
    }

    /**
     * Imprimer une carte individuelle
     */
    public function print(CarteEtudiante $carte)
    {
        $carteInfos = $carte->getCarteInfos();
        
        $pdf = Pdf::loadView('carte.print', compact('carte', 'carteInfos'))
        ->setPaper([0, 0, 242.83, 153.00])
        ->setOptions([
            'dpi' => 300,
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
        ]);
        return $pdf->stream("carte_{$carteInfos['matricule']}.pdf");
    }

    /**
     * Imprimer plusieurs cartes
     */
    public function bulkPrint(Request $request)
    {
        $ids = explode(',', $request->get('ids'));
        $cartes = CarteEtudiante::whereIn('id', $ids)->get();
        
        $cartesData = [];
        foreach ($cartes as $carte) {
            $cartesData[] = [
                'carte' => $carte,
                'infos' => $carte->getCarteInfos()
            ];
        }
        
        $pdf = Pdf::loadView('carte.bulk-print', compact('cartesData'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 300,
                'defaultFont' => 'Arial'
            ]);
        
        return $pdf->stream("cartes_etudiantes_" . now()->format('Y-m-d_H-i-s') . ".pdf");
    }
}