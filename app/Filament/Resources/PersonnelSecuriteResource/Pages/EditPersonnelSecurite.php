<?php

namespace App\Filament\Resources\PersonnelSecuriteResource\Pages;

use App\Filament\Resources\PersonnelSecuriteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class EditPersonnelSecurite extends EditRecord
{
    protected static string $resource = PersonnelSecuriteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateFormDataBeforeFill($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Retirer le mot de passe pour qu'il ne soit pas affiché
        unset($data['password']);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $shouldSendEmail = $data['resend_credentials_email'] ?? false;
        $newPassword = $data['new_password'] ?? null;
        
        if ($newPassword) {
            // Si un nouveau mot de passe est fourni, le hasher
            $data['password'] = Hash::make($newPassword);
            $data['plain_password'] = $newPassword;
            $data['should_send_email'] = $shouldSendEmail;
        } elseif ($shouldSendEmail) {
            // Si pas de nouveau mot de passe mais envoi email demandé, générer un nouveau
            $generatedPassword = $this->generateSecurePassword();
            $data['password'] = Hash::make($generatedPassword);
            $data['plain_password'] = $generatedPassword;
            $data['should_send_email'] = true;
        }
        
        // Nettoyer les champs temporaires
        unset($data['new_password'], $data['resend_credentials_email']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $personnel = $this->record;
        $plainPassword = $this->data['plain_password'] ?? null;
        $shouldSendEmail = $this->data['should_send_email'] ?? false;
        
        if ($plainPassword && $shouldSendEmail) {
            $this->sendCredentialsEmail($personnel, $plainPassword, true);
        }
    }

    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $password = '';
        
        // Assurer au moins un caractère de chaque type
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Compléter avec des caractères aléatoires
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 12; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        return str_shuffle($password);
    }

    private function sendCredentialsEmail($personnel, $password, $isReset = false): void
    {
        try {
            // Utiliser le Job pour l'envoi asynchrone
            \App\Jobs\SendPersonnelCredentialsEmail::dispatch($personnel, $password, $isReset);

            $message = $isReset ? 'Les nouveaux identifiants seront envoyés' : 'Les identifiants seront envoyés';
            
            Notification::make()
                ->title('Email programmé avec succès')
                ->body($message . ' à ' . $personnel->email)
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur lors de l\'envoi de l\'email')
                ->body('Les modifications ont été sauvegardées mais l\'email n\'a pas pu être envoyé.')
                ->danger()
                ->send();
        }
    }
}