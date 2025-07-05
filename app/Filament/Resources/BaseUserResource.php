<?php

// 1. RESSOURCE BASE ABSTRAITE
// app/Filament/Resources/BaseUserResource.php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;


abstract class BaseUserResource extends Resource
{
    protected static ?string $model = User::class;

    /**
     * Champs communs à tous les utilisateurs
     */
    protected static function getBaseFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Informations personnelles')
                ->schema([
                    Forms\Components\TextInput::make('prenom')
                        ->label('Prénom')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('nom')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(        table: 'users',
        column: 'email',
        ignoreRecord: true,
        ignorable: fn ($livewire) => $livewire->record?->user
)

                       ,

                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(20),
       
                ])->columns(2),

            Forms\Components\Section::make('Authentification')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->rule(Password::default())
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state)),
                ])->columns(1),
        ];
    }
    /**
     * Colonnes communes à tous les utilisateurs
     */
    protected static function getBaseTableColumns(): array
    {
        return [
            TextColumn::make('prenom')
                ->label('Prénom')
                ->sortable()
                ->searchable(),

            TextColumn::make('nom')
                ->label('Nom')
                ->sortable()
                ->searchable(),

            TextColumn::make('email')
                ->label('Email')
                ->sortable()
                ->searchable()
                ->copyable(),

            TextColumn::make('phone')
                ->label('Téléphone')
                ->toggleable(),

            TextColumn::make('created_at')
                ->label('Créé le')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
        ];
    }

    /**
     * Filtres communs
     */
    protected static function getBaseFilters(): array
    {
        return [
            SelectFilter::make('genre')
                ->label('Genre')
                ->options([
                    'masculin' => 'Masculin',
                    'feminin' => 'Féminin',
                    'autre' => 'Autre',
                ]),
        ];
    }
}