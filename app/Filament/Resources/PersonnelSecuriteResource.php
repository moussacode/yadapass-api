<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonnelSecuriteResource\Pages;
use App\Filament\Resources\PersonnelSecuriteResource\RelationManagers;
use App\Models\PersonnelSecurite;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use App\Mail\PersonnelCredentialsMail;


class PersonnelSecuriteResource extends Resource
{
    protected static ?string $model = PersonnelSecurite::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationLabel = 'Personnel de Sécurité';
    
    protected static ?string $modelLabel = 'Personnel de Sécurité';
    
    protected static ?string $pluralModelLabel = 'Personnel de Sécurité';
    protected static ?string $navigationGroup = 'Gestion des Utilisateurs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               
                Forms\Components\Section::make('Informations du Personnel de Sécurité')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(table: 'personnel_securites', column: 'email', ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('poste')
                            ->label('Poste')
                            ->nullable(),
                        
                        // Champ informatif pour la création
                        Forms\Components\Placeholder::make('password_info')
                            ->label('Mot de passe')
                            ->content('Un mot de passe sécurisé sera généré automatiquement et envoyé par email.')
                            ->visible(fn (string $context): bool => $context === 'create'),
                        
                        // Champ pour la modification du mot de passe (optionnel)
                        Forms\Components\TextInput::make('new_password')
                            ->label('Nouveau mot de passe (optionnel)')
                            ->password()
                            ->visible(fn (string $context): bool => $context === 'edit')
                            ->helperText('Laissez vide pour conserver le mot de passe actuel. Un nouveau mot de passe sera envoyé par email si modifié.'),
                        
                        // Checkbox pour renvoyer les informations par email
                        Forms\Components\Checkbox::make('send_credentials_email')
                            ->label('Envoyer les informations de connexion par email')
                            ->default(true)
                            ->visible(fn (string $context): bool => $context === 'create'),
                        
                        Forms\Components\Checkbox::make('resend_credentials_email')
                            ->label('Renvoyer les informations de connexion par email')
                            ->default(false)
                            ->visible(fn (string $context): bool => $context === 'edit'),
                    ])->columns(2),
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('poste')
                    ->label('Poste')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resend_credentials')
    ->label('Renvoyer les identifiants')
    ->icon('heroicon-o-envelope')
    ->color('info')
    ->action(function (PersonnelSecurite $record) {
        $newPassword = Str::random(12);

        // Mettre à jour le mot de passe dans la base
        $record->update([
            'password' => Hash::make($newPassword)
        ]);

        try {
            // Envoyer l'email directement (sans job)
            Mail::to($record->email)->send(new PersonnelCredentialsMail($record, $newPassword, true));

            Notification::make()
                ->title('Identifiants renvoyés')
                ->body('Les nouveaux identifiants ont été envoyés à ' . $record->email)
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur d’envoi')
                ->body('Le mot de passe a été modifié, mais l’email n’a pas pu être envoyé.')
                ->danger()
                ->send();
        }
    })

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
 
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonnelSecurites::route('/'),
            'create' => Pages\CreatePersonnelSecurite::route('/create'),
            'edit' => Pages\EditPersonnelSecurite::route('/{record}/edit'),
        ];
    }
}