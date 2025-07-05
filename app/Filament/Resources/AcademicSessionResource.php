<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicSessionResource\Pages;
use App\Filament\Resources\AcademicSessionResource\RelationManagers;
use App\Models\AcademicSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicSessionResource extends Resource
{
    protected static ?string $model = AcademicSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Academie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
            ->label('Nom de la session')
            ->required(),

        Forms\Components\DatePicker::make('start_date')
            ->label('Début')
            ->required(),

        Forms\Components\DatePicker::make('end_date')
            ->label('Fin')
            ->required(),

        Forms\Components\Toggle::make('active')
            ->label('Active')
            ->default(false),

         Forms\Components\Hidden::make('admin_id')
                    ->default(\Illuminate\Support\Facades\Auth::user()?->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('name')->label('Session'),
        Tables\Columns\TextColumn::make('start_date')->label('Début')->date(),
        Tables\Columns\TextColumn::make('end_date')->label('Fin')->date(),
        Tables\Columns\IconColumn::make('active')->label('Active')->boolean(),
        Tables\Columns\TextColumn::make('admin.nom')->label('Créée par'),
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
            'index' => Pages\ListAcademicSessions::route('/'),
            'create' => Pages\CreateAcademicSession::route('/create'),
            'edit' => Pages\EditAcademicSession::route('/{record}/edit'),
        ];
    }
}
