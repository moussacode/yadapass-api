<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoursResource\Pages;
use App\Filament\Resources\CoursResource\RelationManagers;
use App\Models\Cours;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoursResource extends Resource
{
    protected static ?string $model = Cours::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Academie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              Forms\Components\TextInput::make('nom')
    ->label('Nom du cours')
    ->required()
    ->maxLength(255)
    ->live(), // obligatoire pour que $get('nom') fonctionne


        Forms\Components\TextInput::make('code')
    ->label('Code du cours')
    ->required()
    ->maxLength(20)
    ->suffixAction(
        Forms\Components\Actions\Action::make('generateCode')
            ->label('Générer')
            ->icon('heroicon-m-sparkles')
            ->action(function (callable $get, callable $set) {
                $nom = strtoupper($get('nom'));

                if (!$nom) {
                    return; // si nom vide, ne fait rien
                }

                // Générer préfixe à partir des 2 premières lettres de chaque mot
                $mots = explode(' ', $nom);
                $prefix = collect($mots)
                    ->map(fn($mot) => substr($mot, 0, 2))
                    ->implode('');

                // Générer un suffixe numérique unique
                do {
                    $suffix = rand(100, 999);
                    $code = $prefix . $suffix;
                } while (\App\Models\Cours::where('code', $code)->exists());

                $set('code', $code); // injecter le code généré
            })
    ),


        Forms\Components\TextInput::make('enseignant')
            ->label('Enseignant')
            ->nullable()
            ->maxLength(255),

        

        Forms\Components\Hidden::make('admin_id')
            ->default(\Illuminate\Support\Facades\Auth::user()?->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                ->label('Cours')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('code')
                ->label('Code')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('enseignant')
                ->label('Enseignant')
                ->searchable(),

            Tables\Columns\TextColumn::make('classRooms.name')
    ->label('Classe(s)')
    ->formatStateUsing(fn ($state, $record) => $record->classRooms->pluck('name')->join(', '))
    ->sortable()
    ->placeholder('N/A'),
Tables\Columns\TextColumn::make('admin.nom')
                    ->label('Ajouté par')
                    ->sortable()
                    ->searchable()
                    ->placeholder('N/A'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Créé le')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('academic_sessions')
                    ->label('Session(s) académique(s)')
                    ->formatStateUsing(function ($record) {
                        return $record->classRooms()
                            ->with('academicSession')
                            ->get()
                            ->pluck('academicSession.name')
                            ->unique()
                            ->join(', ');
                    })
                    ->sortable(false),
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
            'index' => Pages\ListCours::route('/'),
            'create' => Pages\CreateCours::route('/create'),
            'edit' => Pages\EditCours::route('/{record}/edit'),
        ];
    }
}
