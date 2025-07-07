<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartes Étudiantes - Impression en lot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .carte-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .carte-container {
            width: 350px;
            height: 225px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
            color: white;
            padding: 20px;
            margin: 0 auto;
        }
        
        .carte-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 2px, transparent 2px);
            background-size: 50px 50px;
            opacity: 0.5;
        }
        
        .carte-content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            justify-content: space-between;
        }
        
        .left-section {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .header-section {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .photo-container {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            overflow: hidden;
            background: #f0f0f0;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            width: 100%;
            height: 100%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 12px;
        }
        
        .student-info {
            flex: 1;
        }
        
        .student-name {
            font-size: 18px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 5px;
        }
        
        .student-id {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .details-section {
            font-size: 13px;
            line-height: 1.4;
        }
        
        .details-section p {
            margin-bottom: 3px;
        }
        
        .right-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100px;
        }
        
        .qr-code {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            text-align: center;
            word-break: break-all;
            padding: 8px;
            color: #333;
            margin-bottom: 8px;
        }
        
        .qr-label {
            font-size: 10px;
            text-align: center;
            opacity: 0.8;
        }
        
        .footer {
            position: absolute;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 11px;
            opacity: 0.8;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            background: rgba(255,255,255,0.2);
            margin-top: 5px;
        }
        
        .status-active {
            background: rgba(34, 197, 94, 0.8);
        }
        
        .status-inactive {
            background: rgba(251, 191, 36, 0.8);
        }
        
        .status-expired {
            background: rgba(239, 68, 68, 0.8);
        }
    </style>
</head>
<body>
    @foreach($cartesData->chunk(4) as $pageIndex => $pageCartes)
        @if($pageIndex > 0)
            <div class="page-break"></div>
        @endif
        
        <div class="carte-grid">
            @foreach($pageCartes as $carteData)
                <div class="carte-container">
                    <div class="carte-content">
                        <div class="left-section">
                            <div class="header-section">
                                <div class="photo-container">
                                    @if($carteData['infos']['photo'])
                                        <img src="{{ public_path('storage/' . $carteData['infos']['photo']) }}" alt="Photo étudiant">
                                    @else
                                        <div class="photo-placeholder">
                                            Photo
                                        </div>
                                    @endif
                                </div>
                                <div class="student-info">
                                    <div class="student-name">{{ $carteData['infos']['nom_complet'] }}</div>
                                    <div class="student-id">{{ $carteData['infos']['matricule'] }}</div>
                                </div>
                            </div>
                            
                            <div class="details-section">
                                <p><strong>Classe:</strong> {{ $carteData['infos']['classe'] }}</p>
                                <p><strong>Année:</strong> {{ $carteData['infos']['annee_academique'] }}</p>
                                <p><strong>Émise le:</strong> {{ \Carbon\Carbon::parse($carteData['infos']['date_emission'])->format('d/m/Y') }}</p>
                                <div class="status-badge status-{{ $carteData['infos']['statut'] }}">
                                    {{ ucfirst($carteData['infos']['statut']) }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="right-section">
                            <div class="qr-code">
                                {{ $carteData['infos']['qr_code'] }}
                            </div>
                            <div class="qr-label">Code QR</div>
                        </div>
                    </div>
                    
                    <div class="footer">
                        CARTE ÉTUDIANTE OFFICIELLE
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>