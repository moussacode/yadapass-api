<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoomResource\Pages;
use App\Filament\Resources\ClassRoomResource\RelationManagers;
use App\Filament\Resources\ClassRoomResource\RelationManagers\CoursRelationManager;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationGroup = 'Academie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de la classe')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de la classe')
                            ->required(),

                       

                        Forms\Components\Select::make('academic_session_id')
    ->label('Session académique')
    ->relationship('academicSession', 'name')
    ->required()
    ->default(fn () => \App\Models\AcademicSession::where('active', true)->first()?->id)
,
                        

                               Forms\Components\Hidden::make('admin_id')
            ->default(\Illuminate\Support\Facades\Auth::user()?->id),
            

                        
                    ])->columns(2),
                    
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\TextColumn::make('name')
                    ->label('Nom Classe')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('academicSession.name')
                    ->label('Session académique'),

                Tables\Columns\TextColumn::make('admin.nom')
                    ->label('Administrateur'),

                Tables\Columns\TextColumn::make('cours_count')
                    ->label('Nombre de cours')
                    ->counts('cours'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Filtrer par session académique spécifique
    Tables\Filters\SelectFilter::make('academic_session_id')
        ->label('Session académique')
        ->relationship('academicSession', 'name')
        ->searchable()
        ->default(
            fn () => \App\Models\AcademicSession::where('active', true)->first()?->id
        ),

                
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Modifier')
                    ->color('warning')->outlined()->button(),
                Tables\Actions\ViewAction::make()->label('Info')
                    ->color('success')->outlined()->button(),
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
            CoursRelationManager::make(), // Relation manager for Cours
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('cours');
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'view' => Pages\ViewClassRoom::route('/{record}/info'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }
}
