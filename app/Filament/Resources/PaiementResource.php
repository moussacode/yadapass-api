<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaiementResource\Pages;
use App\Models\Paiement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaiementResource extends Resource
{
    protected static ?string $model = Paiement::class;
 protected static ?string $navigationGroup = 'Academie';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Paiements';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('etudiant_id')
                ->label('Étudiant')
                ->relationship('etudiant', 'matricule')
                ->searchable(['matricule', 'nom', 'prenom'])
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set) => $set('fee_id', null)),

            Forms\Components\Select::make('fee_id')
    ->label('Frais')
    ->options(function (callable $get) {
        $etudiantId = $get('etudiant_id');
        if (!$etudiantId) return [];

        $attribution = \App\Models\Attribution::where('etudiant_id', $etudiantId)->latest()->first();
        if (!$attribution) return [];

        return \App\Models\Fee::where('class_room_id', $attribution->class_room_id)
            ->pluck('nom', 'id');
    })
    
    ->disabled(fn (callable $get) => !$get('etudiant_id') || !\App\Models\Attribution::where('etudiant_id', $get('etudiant_id'))->latest()->first())
    ->helperText(function (callable $get) {
        $etudiantId = $get('etudiant_id');
        if (!$etudiantId) return null;

        $attribution = \App\Models\Attribution::where('etudiant_id', $etudiantId)->latest()->first();
        if (!$attribution) {
            return '⚠️ Cet étudiant n’a pas encore d’attribution. Veuillez l’attribuer à une classe pour afficher les frais.';
        }

        return null;
    })
    ->reactive()
    
    ->required(fn (callable $get) => \App\Models\Attribution::where('etudiant_id', $get('etudiant_id'))->exists()),


            Forms\Components\Placeholder::make('situation_frais')
                ->label('Situation du frais')
                ->columnSpanFull()
                ->content(function (callable $get) {
                    $etudiantId = $get('etudiant_id');
                    $feeId = $get('fee_id');

                    if (!$etudiantId || !$feeId) {
                        return new \Illuminate\Support\HtmlString('<div class="text-sm text-gray-500 p-3 bg-gray-50 rounded-lg">Sélectionnez un étudiant et un frais.</div>');
                    }

                    $fee = \App\Models\Fee::find($feeId);
                    $totalPaye = \App\Models\Paiement::where('etudiant_id', $etudiantId)
                        ->where('fee_id', $feeId)
                        ->sum('montant');

                    $reste = $fee->montant_total - $totalPaye;
                    $pourcentage = ($totalPaye / max($fee->montant_total, 1)) * 100;

                    $status = match (true) {
                        $reste <= 0 => 'Soldé',
                        $totalPaye > 0 => 'Partiellement payé',
                        default => 'Non payé'
                    };

                    $statusColor = match (true) {
                        $reste <= 0 => 'success',
                        $totalPaye > 0 => 'warning',
                        default => 'danger'
                    };

                    $statusClass = match ($statusColor) {
                        'success' => 'bg-green-100 text-green-800',
                        'warning' => 'bg-yellow-100 text-yellow-800',
                        'danger' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    };

                    $html = '
                    <div class="bg-blue-500 rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <!-- Header avec le nom du frais -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">' . e($fee->nom) . '</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ' . $statusClass . '">
                                    ' . e($status) . '
                                </span>
                            </div>
                        </div>
                        
                        <!-- Contenu principal -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Informations financières -->
                                <div class="space-y-4">
                                    <h4 class="text-sm font-medium text-gray-700 uppercase tracking-wide">Détails financiers</h4>
                                    
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                                <span class="text-sm font-medium text-gray-700">Montant total</span>
                                            </div>
                                            <span class="text-lg font-bold text-gray-900">' . number_format($fee->montant_total, 0, ',', ' ') . ' FCFA</span>
                                        </div>
                                        
                                       
                                        
                                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                <span class="text-sm font-medium text-red-700">Reste à payer</span>
                                            </div>
                                            <span class="text-lg font-bold text-red-600">' . number_format($reste, 0, ',', ' ') . ' FCFA</span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>';

                    return new \Illuminate\Support\HtmlString($html);
                })
                ->visible(fn(callable $get) => $get('etudiant_id') && $get('fee_id')),

            Forms\Components\TextInput::make('montant')
                ->label('Montant à payer')
                ->numeric()
                ->required()
                ->minValue(1)
                ->suffix('FCFA')
                ->helperText(function (callable $get) {
                    $etudiantId = $get('etudiant_id');
                    $feeId = $get('fee_id');
                    if (!$etudiantId || !$feeId) return '';

                    $fee = \App\Models\Fee::find($feeId);
                    $totalPaye = \App\Models\Paiement::where('etudiant_id', $etudiantId)
                        ->where('fee_id', $feeId)
                        ->sum('montant');

                    return 'Montant restant : ' . number_format($fee->montant_total - $totalPaye, 0, ',', ' ') . ' FCFA';
                })
                ->maxValue(function (callable $get) {
                    $etudiantId = $get('etudiant_id');
                    $feeId = $get('fee_id');
                    if (!$etudiantId || !$feeId) return null;

                    $fee = \App\Models\Fee::find($feeId);
                    $totalPaye = \App\Models\Paiement::where('etudiant_id', $etudiantId)
                        ->where('fee_id', $feeId)
                        ->sum('montant');

                    return $fee->montant_total - $totalPaye;
                }),

            Forms\Components\DatePicker::make('date')
                ->label('Date du paiement')
                ->required()
                ->default(now()),

            Forms\Components\Hidden::make('admin_id')
                ->default(\Illuminate\Support\Facades\Auth::user()?->id),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('etudiant.matricule')->label('Matricule Étudiant')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('etudiant.nom')->label('Nom Étudiant'),
                Tables\Columns\TextColumn::make('fee.nom')->label('Frais'),
                Tables\Columns\TextColumn::make('montant')->label('Montant')->money('XOF'),
                Tables\Columns\TextColumn::make('date')->label('Date')->date(),
                Tables\Columns\TextColumn::make('admin.name')->label('Enregistré par'),
                Tables\Columns\TextColumn::make('created_at')->label('Créé le')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaiements::route('/'),
            'create' => Pages\CreatePaiement::route('/create'),
            'edit' => Pages\EditPaiement::route('/{record}/edit'),
        ];
    }
}
