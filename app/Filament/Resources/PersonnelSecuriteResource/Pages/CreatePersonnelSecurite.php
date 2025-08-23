<?php

namespace App\Filament\Resources\PersonnelSecuriteResource\Pages;

use App\Filament\Resources\PersonnelSecuriteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use App\Mail\PersonnelCredentialsMail; // ← à ajouter

class CreatePersonnelSecurite extends CreateRecord
{
    protected static string $resource = PersonnelSecuriteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $generatedPassword = $this->generateSecurePassword();
        $data['plain_password'] = $generatedPassword;
        $data['password'] = Hash::make($generatedPassword);
        unset($data['send_credentials_email']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $personnel = $this->record;
        $plainPassword = $this->data['plain_password'] ?? null;

        if ($plainPassword && ($this->data['send_credentials_email'] ?? true)) {
            $this->sendCredentialsEmail($personnel, $plainPassword);
        }
    }

    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 12; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        return str_shuffle($password);
    }

    private function sendCredentialsEmail($personnel, $password): void
    {
        try {
            Mail::to($personnel->email)->send(
                new PersonnelCredentialsMail($personnel, $password)
            );

            Notification::make()
                ->title('Personnel créé avec succès')
                ->body('Les identifiants ont été envoyés à ' . $personnel->email)
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur lors de l\'envoi de l\'email')
                ->body('Le personnel a été créé mais l\'email n\'a pas pu être envoyé.')
                ->danger()
                ->send();
        }
    }
}
