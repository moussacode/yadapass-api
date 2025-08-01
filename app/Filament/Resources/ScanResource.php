<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScanResource\Pages;
use App\Filament\Resources\ScanResource\RelationManagers;
use App\Models\Scan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScanResource extends Resource
{
    protected static ?string $model = Scan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricule')
    ->label('Matricule')
    ->searchable(),
                Tables\Columns\TextColumn::make('date_heure')->label('Date'),
                Tables\Columns\BooleanColumn::make('validation')->label('Validé'),
                Tables\Columns\BadgeColumn::make('statut_acces')
                    ->colors([
                        'success' => 'accepte',
                        'danger' => 'refuse',
                        'warning' => 'en_attente',
                    ])
                    ->label('Accès'),
                
                Tables\Columns\TextColumn::make('commentaire')->limit(30)->label('Commentaire')
                 ->placeholder('Aucun Commentaire'),
               
                Tables\Columns\TextColumn::make('created_at')->label('Créé le')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListScans::route('/'),
            // 'create' => Pages\CreateScan::route('/create'),
            // 'edit' => Pages\EditScan::route('/{record}/edit'),
        ];
    }
}
