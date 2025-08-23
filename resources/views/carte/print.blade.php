<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Carte Étudiante - {{ $carteInfos['nom_complet'] ?? 'Nom Complet' }}</title>
    <style>
        * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    background: white;
    font-size: 3.2mm;
    line-height: 1.2;
}

.page {
    width: 100%;
    background: white;
}

.carte {
    width: 80.6mm;
    height: 48.6mm;
    border: 0.5mm solid #003366;
    padding: 2mm;
    background: #f8f9fa;
    position: relative;
    margin: 0 auto;
    border-radius: 2mm;
}

/* Bande décorative supérieure */
.top-band {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1.5mm;
    background: #003366;
    border-radius: 2mm 2mm 0 0;
}

/* Ligne de sécurité */
.security-line {
    position: absolute;
    top: 1.6mm;
    left: 0;
    right: 0;
    height: 0.3mm;
    background: #004080;
}

/* Section supérieure */
.header {
    width: 100%;
    margin-top: 3mm;
    margin-bottom: 2mm;
    height: 10mm;
    position: relative;
}

/* Logo ESTM (haut droite) */
.logo-section {
    position: absolute;
    top: 0;
    right: 0;
    text-align: right;
    width: 20mm;
}

.logo {
    width: 18mm;
    height: 8mm;
    background: white;
    border: 0.3mm solid #003366;
    border-radius: 1mm;
    margin: 0 0 1mm auto;
    text-align: center;
    vertical-align: middle;
    line-height: 8mm;
}

.logo-placeholder {
    font-size: 2mm;
    color: #003366;
    font-weight: bold;
}

.institution {
    font-size: 2mm;
    font-weight: bold;
    color: #003366;
    text-align: right;
    line-height: 1.1;
}

/* Informations étudiantes (haut gauche) */
.student-header {
    position: absolute;
    top: 0;
    left: 0;
    width: 55mm;
}

.student-year {
    font-size: 2.2mm;
    font-weight: bold;
    color: #003366;
    margin-bottom: 0.5mm;
}

.student-type {
    font-size: 2mm;
    color: #666;
    font-weight: 500;
}

/* Section principale */
.main-content {
    width: 100%;
    margin-bottom: 2mm;
    position: relative;
    height: 28mm;
}

/* Informations étudiant (à gauche) */
.info-section {
    position: absolute;
    left: 0;
    top: 0;
    width: 60mm;
    height: 28mm;
}

.nom {
    font-size: 3mm;
    font-weight: bold;
    color: #003366;
    text-transform: uppercase;
    letter-spacing: 0.2mm;
    margin-bottom: 1mm;
    border-bottom: 0.2mm solid #003366;
    padding-bottom: 0.8mm;
}

.info-row {
    margin-bottom: 0.3mm;
    font-size: 2mm;
    line-height: 1.2;
}

.info-row .label {
    font-weight: bold;
    color: #003366;
    display: inline-block;
    width: 15mm;
    margin-right: 1mm;
}

.info-row .value {
    color: #333;
}

.status {
    font-size: 1.8mm;
    font-weight: bold;
    color: #d9534f;
    background: #f8d7da;
    padding: 0.5mm 1mm;
    border-radius: 1mm;
    display: inline-block;
    margin-top: 1mm;
}

/* Photo (à droite) */
.photo {
    position: absolute;
    right: 0;
    top: 0;
    width: 22mm;
    height: 28mm;
    border: 0.5mm solid #003366;
    background-color: #f5f5f5;
    border-radius: 1mm;
    text-align: center;
    vertical-align: middle;
    line-height: 28mm;
}

.photo-placeholder {
    font-size: 2mm;
    color: #666;
    font-weight: bold;
}

/* QR Code */
.qr {
    position: absolute;
    bottom: 2mm;
    left: 2mm;
    width: 12mm;
    height: 12mm;
    border: 0.3mm solid #003366;
    padding: 0.5mm;
    background: white;
    border-radius: 1mm;
    text-align: center;
    line-height: 10mm;
}

.qr-placeholder {
    font-size: 1.5mm;
    color: #666;
    font-weight: bold;
}

/* Pied de page */
.footer {
    position: absolute;
    bottom: 1mm;
    
    right: 50%;
    padding: 1mm;
    font-size: 1.5mm;
    color: #003366;
    border-top: 0.2mm solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.school-info {
    display: flex;
    flex-direction: column;
    gap: 0.5mm;
}

.school-location {
    font-weight: bold;
}

.school-contact {
    font-size: 1.4mm;
    color: #666;
}


        .official-text {
            float: right;
            font-size: 5px;
            font-weight: bold;
            color: #003366;
            margin-top: 3px;
        }

        .clearfix {
            clear: both;
        }

        /* Ligne de sécurité */
        .security-line {
            position: absolute;
            top: 6px;
            left: 0;
            right: 0;
            height: 1px;
            background: #004080;
        }

        /* Styles pour les images si présentes */
        .logo img,
        .photo img,
        .qr img {
            max-width: 100%;
            max-height: 100%;


            object-fit: cover;
        }
    </style>
</head>
<body>
@php
// Configuration mémoire pour DomPDF
ini_set('memory_limit', '256M');

// Fonction pour traiter les images de manière efficace
function processImage($imagePath) {
    // Image transparente 1x1 par défaut
    $transparentImage = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    
    if (empty($imagePath) || !file_exists($imagePath)) {
        return $transparentImage;
    }

    try {
        // Lecture par chunks pour éviter les problèmes de mémoire
        $handle = fopen($imagePath, 'rb');
        if (!$handle) {
            return $transparentImage;
        }
        
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192); // Lecture par blocs de 8KB
        }
        fclose($handle);

        return base64_encode($contents);
    } catch (Exception $e) {
        return $transparentImage;
    }
}

// Fonction pour déterminer le type MIME d'une image
function getImageMimeType($imagePath) {
    if (!file_exists($imagePath)) {
        return 'image/png';
    }
    
    $info = getimagesize($imagePath);
    return $info['mime'] ?? 'image/png';
}

// Traitement des images
$logoPath = '';
$photoPath = '';
$qrPath = '';

$logoPath = public_path('storage/' . 'ESTM.png'); // Changé en PNG pour meilleure compatibilité

if (isset($carteInfos['photo']) && $carteInfos['photo']) {
    $photoPath = public_path('storage/' . $carteInfos['photo']);
}

if (isset($carteInfos['qr_data']) && $carteInfos['qr_data']) {
    $qrPath = public_path('storage/' . $carteInfos['qr_data']);
}

// Encodage base64 des images
$logoBase64 = processImage($logoPath);
$photoBase64 = processImage($photoPath);
$qrBase64 = processImage($qrPath);

// Types MIME
$logoMime = getImageMimeType($logoPath);
$photoMime = getImageMimeType($photoPath);
$qrMime = getImageMimeType($qrPath);
@endphp

    <div class="page">
        <div class="carte">
            <!-- Bande décorative supérieure -->
            <div class="top-band"></div>
            
            <!-- Ligne de sécurité -->
            <div class="security-line"></div>
            
            <!-- Section supérieure -->
            <div class="header">
                <!-- Informations étudiantes (haut gauche) -->
                <div class="student-header">
                    <div class="student-year">{{ $carteInfos['annee_academique'] ?? '2024-2025' }}</div>
                    <div class="student-type">CARTE ÉTUDIANTE</div>
                </div>
                
                <!-- Logo et nom école (haut droite) -->
                <div class="logo-section">
                    <div class="logo">
                        @if($logoBase64 && $logoPath && file_exists($logoPath))
                            <img src="data:{{ $logoMime }};base64,{{ $logoBase64 }}" alt="Logo ESTM">
                        @else
                            <div class="logo-placeholder">ESTM</div>
                        @endif
                    </div>
                    
                </div>
            </div>
            
            <!-- Section principale -->
            <div class="main-content">
                <!-- Informations étudiant (à gauche) -->
                <div class="info-section">
                    <div class="nom">{{ $carteInfos['nom_complet'] ?? 'NOM COMPLET' }}</div>
                    
                    <div class="info-row">
                        <span class="label">Matricule:</span>
                        <span class="value">{{ $carteInfos['matricule'] ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="label">Classe:</span>
                        <span class="value">{{ $carteInfos['classe'] ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="label">Émise le:</span>
                        <span class="value">
                            @if(isset($carteInfos['date_emission']))
                                {{ \Carbon\Carbon::parse($carteInfos['date_emission'])->format('d/m/Y') }}
                            @else
                                {{ date('d/m/Y') }}
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Date de nais:</span>
                        <span class="value">
                            @if(isset($carteInfos['date_naissance']))
                                {{ \Carbon\Carbon::parse($carteInfos['date_naissance'])->format('d/m/Y') }}
                            @else
                                {{ "N/A" }}
                            @endif
                        </span>
                    </div>
                    
                    
                    @if(isset($carteInfos['statut']) && $carteInfos['statut'])
                        <div class="status">{{ strtoupper($carteInfos['statut']) }}</div>
                    @endif
                </div>
                
                <!-- Photo (à droite) -->
                <div class="photo">
                    @if($photoBase64 && $photoPath && file_exists($photoPath))
                        <img src="data:{{ $photoMime }};base64,{{ $photoBase64 }}" alt="Photo étudiant">
                    @else
                        <div class="photo-placeholder">PHOTO</div>
                    @endif
                </div>
            </div>

            <!-- QR Code -->
            <div class="qr">
                @if($qrBase64 && $qrPath && file_exists($qrPath))
                    <img src="data:{{ $qrMime }};base64,{{ $qrBase64 }}" alt="QR Code">
                @else
                    <div class="qr-placeholder">QR</div>
                @endif
            </div>

            <!-- Pied de page avec informations école -->
            <div class="footer">
                <div class="footer-content">
                    <div class="school-info">
                        <div class="school-location">Dakar, Sénégal</div>
                        <div class="school-contact">
                            contact@estm.sn • www.estm.sn
                        </div>
                    </div>
                    <!-- <div class="official-text">OFFICIELLE</div> -->
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>