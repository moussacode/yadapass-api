<?php

namespace App\Filament\Resources\ClassRoomResource\Pages;

use App\Filament\Resources\ClassRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;

class ViewClassRoom extends ViewRecord
{
    
    protected static string $resource = ClassRoomResource::class;

public  function table(Table $table): Table
{
    return $table
        ->columns([
            
            Tables\Columns\TextColumn::make('name')
                ->label('Nom de la classe')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('academicSession.name')
                ->label('Session académique')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('admin.name')
                ->label('Administrateur')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->label('Créé le'),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->label('Mis à jour le'),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            // ...
        ]);
}
}
