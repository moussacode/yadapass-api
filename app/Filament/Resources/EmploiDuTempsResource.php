<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmploiDuTempsResource\Pages;
use App\Models\EmploiDuTemps;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmploiDuTempsResource extends Resource
{
    protected static ?string $model = EmploiDuTemps::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Emploi du temps';
    protected static ?string $pluralLabel = 'Emplois du temps';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations du cours')
                ->schema([
                    Forms\Components\Select::make('class_room_id')
                        ->label('Classe')
                        ->relationship('classRoom', 'name')
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('cours_id')
                        ->label('Cours')
                        ->relationship('cours', 'nom')
                        ->required()
                        ->searchable(),


                ])->columns(3),

            Forms\Components\Section::make('Horaires')
                ->schema([
                    Forms\Components\Select::make('jour')
                        ->label('Jour')
                        ->options([
                            'Lundi' => 'Lundi',
                            'Mardi' => 'Mardi',
                            'Mercredi' => 'Mercredi',
                            'Jeudi' => 'Jeudi',
                            'Vendredi' => 'Vendredi',
                            'Samedi' => 'Samedi',
                        ])
                        ->required(),

                    Forms\Components\TimePicker::make('heure_debut')
                        ->label('Heure de début')
                        ->required()
                        ->seconds(false),

                    Forms\Components\TimePicker::make('heure_fin')
                        ->label('Heure de fin')
                        ->required()
                        ->seconds(false)
                        ->after('heure_debut'),

                    Forms\Components\Select::make('salle_id')
                        ->relationship('salle', 'nom')
                        ->label('Salle')
                        
                ])->columns(4),

            
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cours.nom')
                    ->label('Cours')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('jour')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lundi' => 'info',
                        'Mardi' => 'success',
                        'Mercredi' => 'warning',
                        'Jeudi' => 'danger',
                        'Vendredi' => 'gray',
                        'Samedi' => 'primary',
                    }),

                Tables\Columns\TextColumn::make('creneau')
                    ->label('Créneaux')
                    ->getStateUsing(fn(EmploiDuTemps $record) => $record->creneau),

                Tables\Columns\TextColumn::make('cours.enseignant')
                    ->label('Enseignant')
                    ->getStateUsing(fn($record) => $record->cours->enseignant ?? 'Non défini')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('salle.nom')
                    ->label('Salle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duree')
                    ->label('Durée')
                    ->getStateUsing(fn(EmploiDuTemps $record) => $record->duree . ' min')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Classe')
                    ->relationship('classRoom', 'name'),

                Tables\Filters\SelectFilter::make('jour')
                    ->options([
                        'Lundi' => 'Lundi',
                        'Mardi' => 'Mardi',
                        'Mercredi' => 'Mercredi',
                        'Jeudi' => 'Jeudi',
                        'Vendredi' => 'Vendredi',
                        'Samedi' => 'Samedi',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('jour')
            ->defaultSort('heure_debut');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmploiDuTemps::route('/'),
            'create' => Pages\CreateEmploiDuTemps::route('/create'),
            'edit' => Pages\EditEmploiDuTemps::route('/{record}/edit'),
            'calendar' => Pages\CalendarEmploiDuTemps::route('/calendar'),
        ];
    }
}
