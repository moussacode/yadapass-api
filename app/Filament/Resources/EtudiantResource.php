<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EtudiantResource\Pages;
use App\Models\Etudiant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EtudiantResource extends Resource
{
    protected static ?string $model = Etudiant::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Gestion des Utilisateurs';
    protected static ?string $navigationLabel = 'Étudiants';
    protected static ?string $modelLabel = 'Étudiant';
    protected static ?string $pluralModelLabel = 'Étudiants';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('prenom')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('nom')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('matricule')
                    ->label('Matricule')
                 
                    ->unique(ignoreRecord: true),

                FileUpload::make('photo')
                    ->label('Photo de l\'étudiant')
                    ->nullable()
                    ->imagePreviewHeight('250')
                    ->maxSize(1024)
                    ->disk('public')
                    ->directory('etudiants/photos'),

                Forms\Components\TextInput::make('adresse')
                    ->label('Adresse')
                    ->nullable(),

                Forms\Components\TextInput::make('telephone')
                    ->label('Téléphone')
                    ->nullable()
                    ->tel()
                    ->maxLength(20),

                Forms\Components\DatePicker::make('date_naissance')
                    ->label('Date de naissance')
                    ->required()
                    ->maxDate(now()->subYears(14)),

                Forms\Components\Select::make('genre')
                    ->label('Genre')
                    ->options([
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                        'Autre' => 'Autre',
                    ])
                    ->nullable(),

                Forms\Components\TextInput::make('nationalite')
                    ->label('Nationalité')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricule')->label('Matricule')->searchable(),
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('prenom')->label('Prénom')->searchable(),
                Tables\Columns\TextColumn::make('date_naissance')->label('Date de naissance')->date(),
                Tables\Columns\TextColumn::make('genre')->label('Genre')->placeholder('N/A'),
                Tables\Columns\TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y'),
            ])
            ->filters([
                // à ajouter plus tard si nécessaire
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
            'index' => Pages\ListEtudiants::route('/'),
            'create' => Pages\CreateEtudiant::route('/create'),
            'edit' => Pages\EditEtudiant::route('/{record}/edit'),
        ];
    }
}
