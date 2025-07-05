<?php

namespace App\Filament\Resources\ClassRoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoursRelationManager extends RelationManager
{
    protected static string $relationship = 'Cours';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\TextInput::make('nom')->required()->maxLength(255),
                Forms\Components\TextInput::make('code')->required()->maxLength(20),
                Forms\Components\TextInput::make('enseignant')->maxLength(255),
                 Forms\Components\Hidden::make('admin_id')
            ->default(\Illuminate\Support\Facades\Auth::user()?->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nom')
            ->columns([
                 Tables\Columns\TextColumn::make('nom'),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('enseignant'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),      // ← Permet d’attacher un cours existant
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\DetachAction::make(), // ← Si tu veux permettre de détacher aussi
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
