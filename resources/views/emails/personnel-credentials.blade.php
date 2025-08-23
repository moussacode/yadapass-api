<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identifiants de connexion</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .credentials-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .credential-label {
            font-weight: bold;
            color: #495057;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            color: #28a745;
            font-size: 16px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-icon {
            color: #856404;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $isReset ? 'Nouveaux identifiants' : 'Identifiants de connexion' }}</h1>
            <p>Système de gestion - Personnel de Sécurité</p>
        </div>

        <p>Bonjour {{ $personnel->prenom }} {{ $personnel->nom }},</p>

        @if($isReset)
            <p>Vos identifiants de connexion ont été réinitialisés. Voici vos nouveaux identifiants :</p>
        @else
            <p>Votre compte a été créé avec succès. Voici vos identifiants de connexion :</p>
        @endif

        <div class="credentials-box">
            <div class="credential-item">
                <div class="credential-label">Email / Nom d'utilisateur :</div>
                <div class="credential-value">{{ $personnel->email }}</div>
            </div>
            
            <div class="credential-item">
                <div class="credential-label">Mot de passe :</div>
                <div class="credential-value">{{ $password }}</div>
            </div>
            
            @if($personnel->poste)
            <div class="credential-item">
                <div class="credential-label">Poste :</div>
                <div class="credential-value">{{ $personnel->poste }}</div>
            </div>
            @endif
        </div>

        <div class="warning">
            <div class="warning-icon">⚠️ Important :</div>
            <ul>
                <li><strong>Changez votre mot de passe</strong> lors de votre première connexion</li>
                <li><strong>Ne partagez jamais</strong> vos identifiants avec d'autres personnes</li>
                <li><strong>Gardez ce mot de passe en lieu sûr</strong> et supprimez cet email après l'avoir noté</li>
                <li>En cas de problème, contactez l'administrateur système</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/admin" class="btn">Accéder au système</a>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Ne répondez pas à ce message.</p>
            <p>Si vous n'êtes pas {{ $personnel->prenom }} {{ $personnel->nom }}, veuillez ignorer cet email.</p>
            <p><small>© {{ date('Y') }} - Système de gestion sécurisé</small></p>
        </div>
    </div>
</body>
</html>