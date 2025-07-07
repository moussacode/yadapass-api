<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prévisualisation - Carte Étudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .carte-container {
            width: 350px;
            height: 225px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        
        .carte-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/></svg>');
            background-size: 50px 50px;
            opacity: 0.3;
        }
        
        .photo-container {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            overflow: hidden;
            background: #f0f0f0;
        }
        
        .qr-code {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            text-align: center;
            word-break: break-all;
            padding: 5px;
        }
        
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <!-- Boutons d'action -->
        <div class="mb-6 flex justify-center space-x-4 no-print">
            <button onclick="window.print()" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                Imprimer
            </button>
            <a href="{{ route('carte.print', $carte) }}" target="_blank" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">
                Télécharger PDF
            </a>
            <button onclick="window.close()" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                Fermer
            </button>
        </div>

        <!-- Carte recto -->
        <div class="flex justify-center mb-8">
            <div class="carte-container text-white p-4 relative">
                <div class="flex justify-between items-start h-full">
                    <!-- Section gauche avec photo et infos -->
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="photo-container mr-4">
                                @if($carteInfos['photo'])
                                    <img src="{{ asset('storage/' . $carteInfos['photo']) }}" 
                                         alt="Photo" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">Photo</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h2 class="text-lg font-bold leading-tight">{{ $carteInfos['nom_complet'] }}</h2>
                                <p class="text-sm opacity-90">{{ $carteInfos['matricule'] }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-1 text-sm">
                            <p><span class="font-semibold">Classe:</span> {{ $carteInfos['classe'] }}</p>
                            <p><span class="font-semibold">Année:</span> {{ $carteInfos['annee_academique'] }}</p>
                            <p><span class="font-semibold">Émise le:</span> {{ \Carbon\Carbon::parse($carteInfos['date_emission'])->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    <!-- Section droite avec QR Code -->
                    <div class="flex flex-col items-center">
                        <div class="qr-code mb-2">
                          <img src="{{ asset('storage/' . $carteInfos['qr_data']) }}" alt="QR Code" width="100">
                        </div>
                        <p class="text-xs text-center opacity-75">Code QR</p>
                    </div>
                </div>
                
                <!-- Logo/Nom de l'établissement -->
                <div class="absolute bottom-2 left-4 right-4 text-center">
                    <p class="text-xs font-semibold opacity-75">CARTE ÉTUDIANTE</p>
                </div>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="bg-white rounded-lg shadow-lg p-6 no-print">
            <h3 class="text-lg font-semibold mb-4">Informations détaillées</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p><strong>Matricule:</strong> {{ $carteInfos['matricule'] }}</p>
                    <p><strong>Nom complet:</strong> {{ $carteInfos['nom_complet'] }}</p>
                    <p><strong>Classe:</strong> {{ $carteInfos['classe'] }}</p>
                </div>
                <div>
                    <p><strong>Année académique:</strong> {{ $carteInfos['annee_academique'] }}</p>
                    <p><strong>Date d'émission:</strong> {{ \Carbon\Carbon::parse($carteInfos['date_emission'])->format('d/m/Y') }}</p>
                    <p><strong>Statut:</strong> 
                        <span class="px-2 py-1 rounded text-xs 
                            @if($carteInfos['statut'] === 'active') bg-green-100 text-green-800
                            @elseif($carteInfos['statut'] === 'inactive') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($carteInfos['statut']) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>