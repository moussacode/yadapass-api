<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributionResource\Pages;
use App\Filament\Resources\AttributionResource\RelationManagers;
use App\Models\Attribution;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttributionResource extends Resource
{
    protected static ?string $model = Attribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationGroup = 'Academie';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('etudiant_id')
                    ->label('Matricule Étudiant')
                    ->relationship('etudiant', 'matricule')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $etudiant = \App\Models\Etudiant::find($state);
                        $set('etudiant_nom', $etudiant?->nom);
                    }),

                TextInput::make('etudiant_nom')
                    ->label('Nom Étudiant')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('class_room_id')
                    ->label('Classe')
                    ->relationship('classRoom', 'name')
                    ->required(),

                Forms\Components\Select::make('academic_session_id')
                    ->label('Session académique')
                    ->relationship('academicSession', 'name')
                    ->required()
                    ->default(fn() => \App\Models\AcademicSession::where('active', true)->first()?->id),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('etudiant.matricule')
                    ->label('Matricule Étudiant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('etudiant.nom_complet')
                    ->label('Nom de l’étudiant')
                    ->formatStateUsing(fn($state, $record) => $record->etudiant->nom . ' ' . $record->etudiant->prenom)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicSession.name')
                    ->label('Session académique')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('class_room_id')
                    ->label('Classe')
                    ->relationship('classRoom', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                
                Action::make('view_card')
                    ->label('Voir carte')
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->visible(fn ($record) => $record->carteEtudiante)
                    ->url(fn ($record) => route('carte.preview', $record->carteEtudiante))
                    ->openUrlInNewTab(),

                Action::make('print_card')
                    ->label('Imprimer carte')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn ($record) => $record->carteEtudiante)
                    ->url(fn ($record) => route('carte.print', $record->carteEtudiante))
                    ->openUrlInNewTab(),

                Action::make('generate_card')
                    ->label('Générer carte')
                    ->icon('heroicon-o-plus')
                    ->color('warning')
                    ->visible(fn ($record) => !$record->carteEtudiante)
                    ->action(function ($record) {
                        \App\Models\CarteEtudiante::create([
                            'attribution_id' => $record->id,
                            'qr_code' => strtoupper($record->etudiant->matricule),
                            'qr_data' => strtoupper($record->etudiant->matricule),
                            'statut' => 'active',
                            'date_emission' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Générer la carte étudiante')
                    ->modalDescription('Êtes-vous sûr de vouloir générer une carte pour cet étudiant ?')
                    ->modalSubmitActionLabel('Générer'),
                
                Action::make('resend_card_email')
    ->label('Renvoyer email')
    ->icon('heroicon-o-envelope')
    ->color('primary')
    ->visible(fn ($record) => $record->carteEtudiante)
    ->action(function ($record) {
        $etudiant = $record->etudiant;
        $carte = $record->carteEtudiante;

        if ($etudiant && $etudiant->email && $carte) {
            $etudiant->notify(new \App\Notifications\CarteEtudianteCreee($etudiant, $carte));
            \Filament\Notifications\Notification::make()
                ->title('📧 Email envoyé')
                ->success()
                ->body("La carte a été renvoyée à {$etudiant->email}.")
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('❌ Échec de l’envoi')
                ->danger()
                ->body('Impossible d’envoyer l’email (étudiant sans email ou carte manquante).')
                ->send();
        }
    })
    ->requiresConfirmation()
    ->modalHeading('Renvoyer la carte par email')
    ->modalDescription('Souhaitez-vous renvoyer la carte étudiante à l’adresse email de cet étudiant ?')
    ->modalSubmitActionLabel('Envoyer')
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
            'index' => Pages\ListAttributions::route('/'),
            'create' => Pages\CreateAttribution::route('/create'),
            'edit' => Pages\EditAttribution::route('/{record}/edit'),
        ];
    }
}
