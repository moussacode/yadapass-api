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

                Forms\Components\TextInput::make('email')
                    ->label('Email'),

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
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->disk('public')
                    ->height(40)
                    ->width(40)
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                
                Tables\Columns\TextColumn::make('matricule')
                    ->label('Matricule')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('date_naissance')
                    ->label('Date de naissance')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('genre')
                    ->label('Genre')
                    
                    ->color(fn (string $state): string => match ($state) {
                        'Homme' => 'blue',
                        'Femme' => 'pink',
                        default => 'gray',
                    })
                    ->placeholder('N/A'),
                
                // Colonne pour afficher l'attribution actuelle
                Tables\Columns\TextColumn::make('attribution_actuelle')
                    ->label('Classe actuelle')
                    ->getStateUsing(function (Etudiant $record): ?string {
                        $attribution = \App\Models\Attribution::where('etudiant_id', $record->id)
                            ->with('classRoom')
                            ->latest()
                            ->first();
                        return $attribution?->classRoom?->name ?? 'Non attribué';
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Non attribué' ? 'danger' : 'success'),
                
            
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('genre')
                    ->options([
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                        'Autre' => 'Autre',
                    ]),
                
                Tables\Filters\Filter::make('avec_paiements')
                    ->label('Avec paiements')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereHas('paiements')
                    ),
                
                Tables\Filters\Filter::make('sans_paiements')
                    ->label('Sans paiements')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereDoesntHave('paiements')
                    ),
                
                Tables\Filters\Filter::make('avec_attribution')
                    ->label('Avec attribution')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereHas('attributions')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\EditAction::make()
                    ->label('Modifier')
                    ->icon('heroicon-o-pencil-square'),
                
                Tables\Actions\Action::make('nouveau_paiement')
                    ->label('Paiement')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->url(fn (Etudiant $record): string => '/admin/paiements/create?etudiant_id=' . $record->id),
                
                Tables\Actions\Action::make('voir_paiements')
                    ->label('Ses paiements')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->url(fn (Etudiant $record): string => '/admin/paiements?tableFilters[etudiant_id][value]=' . $record->id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'view' => Pages\ViewEtudiant::route('/{record}'),
            'edit' => Pages\EditEtudiant::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['matricule', 'nom', 'prenom', 'email'];
    }
}