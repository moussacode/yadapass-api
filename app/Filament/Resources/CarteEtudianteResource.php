<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarteEtudianteResource\Pages;
use App\Models\CarteEtudiante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class CarteEtudianteResource extends Resource
{
    protected static ?string $model = CarteEtudiante::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Cartes Étudiantes';
    protected static ?string $pluralModelLabel = 'Cartes Étudiantes';
     protected static ?string $navigationGroup = 'Etudiants';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attribution_id')
                    ->label('Attribution')
                    ->relationship('attribution', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        $record->etudiant->matricule . ' - ' . 
                        $record->etudiant->nom . ' ' . 
                        $record->etudiant->prenom
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('qr_code')
                    ->label('Code QR')
                    ->required(),

                Forms\Components\Select::make('statut')
                    ->label('Statut')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expirée',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('date_emission')
                    ->label('Date d\'émission')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('attribution.etudiant.photo')
                    ->label('Photo')
                    ->circular()
                    ->size(50),

                Tables\Columns\TextColumn::make('attribution.etudiant.matricule')
                    ->label('Matricule')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('attribution.etudiant.nom_complet')
                    ->label('Nom complet')
                    ->formatStateUsing(fn($state, $record) => 
                        $record->attribution->etudiant->nom . ' ' . 
                        $record->attribution->etudiant->prenom
                    )
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('attribution.classRoom.name')
                    ->label('Classe')
                    ->sortable(),

                Tables\Columns\TextColumn::make('attribution.academicSession.name')
                    ->label('Session académique')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('statut')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'expired',
                    ]),

                Tables\Columns\TextColumn::make('date_emission')
                    ->label('Date d\'émission')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expirée',
                    ]),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Prévisualiser')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (CarteEtudiante $record): string => route('carte.preview', $record))
                    ->openUrlInNewTab(),

                Action::make('print')
                    ->label('Imprimer')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (CarteEtudiante $record): string => route('carte.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('bulk_print')
                        ->label('Imprimer sélectionnées')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->toArray();
                            return redirect()->route('carte.bulk-print', ['ids' => implode(',', $ids)]);
                        }),
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
            'index' => Pages\ListCarteEtudiantes::route('/'),
            'create' => Pages\CreateCarteEtudiante::route('/create'),
            'edit' => Pages\EditCarteEtudiante::route('/{record}/edit'),
            
        ];
    }
}