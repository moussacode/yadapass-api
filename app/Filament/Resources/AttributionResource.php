<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributionResource\Pages;
use App\Filament\Resources\AttributionResource\RelationManagers;
use App\Models\Attribution;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttributionResource extends Resource
{
    protected static ?string $model = Attribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('etudiant_id')
                    ->label('Matricule Étudiant')
                    ->relationship('etudiant', 'matricule')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $etudiant = \App\Models\Etudiant::find($state);
                        $set('etudiant_nom', $etudiant?->nom);
                    }),

                TextInput::make('etudiant_nom')
                    ->label('Nom Étudiant')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('class_room_id')
                    ->label('Classe')
                    ->relationship('classRoom', 'name')
                    ->required(),

                Forms\Components\Select::make('academic_session_id')
                    ->label('Session académique')
                    ->relationship('academicSession', 'name')
                    ->required()
                    ->default(fn() => \App\Models\AcademicSession::where('active', true)->first()?->id),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('etudiant.matricule')
                    ->label('Matricule Étudiant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('etudiant.nom_complet')
                    ->label('Nom de l’étudiant')
                    ->formatStateUsing(fn($state, $record) => $record->etudiant->nom . ' ' . $record->etudiant->prenom)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicSession.name')
                    ->label('Session académique')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('class_room_id')
                    ->label('Classe')
                    ->relationship('classRoom', 'name')
                    ->searchable(),
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
            'index' => Pages\ListAttributions::route('/'),
            'create' => Pages\CreateAttribution::route('/create'),
            'edit' => Pages\EditAttribution::route('/{record}/edit'),
        ];
    }
}
