<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministrateurResource\Pages;
use App\Filament\Resources\AdministrateurResource\RelationManagers;
use App\Models\Administrateur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdministrateurResource extends Resource
{
    protected static ?string $model = Administrateur::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestion des Utilisateurs';
    protected static ?string $navigationLabel = 'Administrateurs';
    protected static ?string $modelLabel = 'Administrateur';
    protected static ?string $pluralModelLabel = 'Administrateurs';

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
                //
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
            'index' => Pages\ListAdministrateurs::route('/'),
            'create' => Pages\CreateAdministrateur::route('/create'),
            'edit' => Pages\EditAdministrateur::route('/{record}/edit'),
        ];
    }
}
