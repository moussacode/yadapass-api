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
use Illuminate\Validation\Rules\Password;

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
                            ->unique(table: 'users', column: 'email', ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('poste')
                            ->label('Poste')
                            ->nullable(),
                        
                       Forms\Components\TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->rule(Password::min(8))
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state)), 
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
