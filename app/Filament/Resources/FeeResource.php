<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
use App\Filament\Resources\FeeResource\RelationManagers;
use App\Models\Fee;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Frais';
    protected static ?string $pluralModelLabel = 'Frais';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('class_room_id')
                    ->relationship('classRoom', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        // Réinitialiser le type quand la classe change
                        $set('type', null);
                        $set('nom', null);
                    }),
                    
                Forms\Components\Select::make('type')
                    ->options(function (Get $get) {
                        $classRoomId = $get('class_room_id');
                        if (!$classRoomId) {
                            return [
                                'inscription' => 'Frais d\'inscription',
                                'scolarite' => 'Frais de scolarité',
                            ];
                        }
                        
                        // Récupérer les types déjà existants pour cette classe
                        $existingTypes = Fee::where('class_room_id', $classRoomId)
                            ->pluck('type')
                            ->toArray();
                        
                        $allOptions = [
                            'inscription' => 'Frais d\'inscription',
                            'scolarite' => 'Frais de scolarité',
                        ];
                        
                        // Retirer les types déjà existants des options
                        return array_diff_key($allOptions, array_flip($existingTypes));
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $classRoomId = $get('class_room_id');
                        if ($classRoomId && $state) {
                            // Générer automatiquement le nom basé sur le type et la classe
                            $classRoom = ClassRoom::find($classRoomId);
                            if ($classRoom) {
                                $typeName = $state === 'inscription' ? 'Frais d\'inscription' : 'Frais de scolarité';
                                $set('nom', $typeName . ' ' . $classRoom->name);
                            }
                        }
                    })
                    ->disabled(function (Get $get) {
                        $classRoomId = $get('class_room_id');
                        return !$classRoomId;
                    })
                    ->helperText(function (Get $get) {
                        $classRoomId = $get('class_room_id');
                        if (!$classRoomId) {
                            return 'Veuillez d\'abord sélectionner une classe.';
                        }
                        
                        $existingTypes = Fee::where('class_room_id', $classRoomId)
                            ->pluck('type')
                            ->toArray();
                        
                        if (empty($existingTypes)) {
                            return 'Tous les types de frais sont disponibles.';
                        }
                        
                        $existingLabels = [];
                        foreach ($existingTypes as $type) {
                            $existingLabels[] = $type === 'inscription' ? 'Frais d\'inscription' : 'Frais de scolarité';
                        }
                        
                        return 'Déjà configuré(s) : ' . implode(', ', $existingLabels);
                    }),
                    
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->maxLength(255)
                    ->disabled() // Le nom est généré automatiquement
                    ->dehydrated() // Mais on veut quand même le sauvegarder
                    ->rules([
                        function (Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $classRoomId = $get('class_room_id');
                                $type = $get('type');
                                
                                if ($classRoomId && $type) {
                                    $exists = Fee::where('class_room_id', $classRoomId)
                                        ->where('type', $type)
                                        ->exists();
                                    
                                    if ($exists) {
                                        $typeName = $type === 'inscription' ? 'frais d\'inscription' : 'frais de scolarité';
                                        $fail("Un {$typeName} existe déjà pour cette classe.");
                                    }
                                }
                            };
                        }
                    ]),
                    
                Forms\Components\TextInput::make('montant_total')
                    ->numeric()
                    ->required()
                    ->prefix('XOF'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom du frais')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return $state === 'inscription' ? 'Frais d\'inscription' : 'Frais de scolarité';
                    }),
                Tables\Columns\TextColumn::make('montant_total')
                    ->money('XOF')
                    ->label('Montant'),
                Tables\Columns\TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Créé le')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'inscription' => 'Frais d\'inscription',
                        'scolarite' => 'Frais de scolarité',
                    ])
                    ->label('Type de frais'),
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->relationship('classRoom', 'name')
                    ->label('Classe'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }
}