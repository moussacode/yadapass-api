<?php

namespace App\Filament\Resources\EtudiantResource\Pages;

use App\Filament\Resources\EtudiantResource;
use App\Models\Etudiant;
use App\Models\Paiement;
use App\Models\Attribution;
use App\Models\Fee;
use Filament\Actions;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;

use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Components\ViewEntry;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;

class ViewEtudiant extends ViewRecord
{
    protected static string $resource = EtudiantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Modifier l\'étudiant')
                ->icon('heroicon-o-pencil-square'),
            
            Actions\Action::make('voir_paiements')
                ->label('Voir les paiements')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->url(fn (Etudiant $record): string => '/admin/paiements?tableFilters[etudiant_id][value]=' . $record->id),
            
            Actions\Action::make('nouveau_paiement')
                ->label('Nouveau paiement')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url('/admin/paiements/create')
                ->openUrlInNewTab(false),
            
            Actions\Action::make('attribuer_classe')
                ->label('Gérer les attributions')
                ->icon('heroicon-o-academic-cap')
                ->color('warning')
                ->url(fn (Etudiant $record): string => '/admin/attributions?tableFilters[etudiant_id][value]=' . $record->id),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Section Informations personnelles
        Section::make('Informations personnelles')
    ->icon('heroicon-o-user')
    ->schema([
        Grid::make(12)
            ->schema([
                // Colonne gauche : Photo et infos courtes
                Group::make([
                    ImageEntry::make('photo')
                        ->disk('public')
                        ->defaultImageUrl(url('/images/default-avatar.png'))
                        ->circular()
                        ->height(200)
                        ->width(200)
                        ->alignCenter(),

                    TextEntry::make('matricule')
                        ->label('')
                        ->badge()
                        ->color('primary')
                    
                        ->alignCenter(),

                    
                ])
                ->columnSpan(3),

                // Colonne droite : détails
                Group::make([
                    Grid::make(3)->schema([
                        TextEntry::make('nom')
                        ->label('Nom')
                        ,
                         TextEntry::make('prenom')
                        ->label('Prénom')
                       
                        
                        ,
                        TextEntry::make('nationalite')
                            ->label('Nationalité')
                            ->badge()
                            ->color('success')
                            ->placeholder('Non renseignée'),
                        
                        TextEntry::make('date_naissance')
                            ->label('Date de naissance')
                            // ->formatStateUsing(fn ($state, $record) =>
                            //     $state ? $state->format('d/m/Y') . ' à ' . ($record->lieu_naissance ?? 'Non renseigné') : 'Non renseignée'
                            // )
                            ->icon('heroicon-o-calendar'),

                         TextEntry::make('adresse')
                            ->label('Adresse')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('Non renseignée'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable()
                            ->icon('heroicon-o-envelope')
                            ->color('primary'),

                         TextEntry::make('telephone')
                            ->label('Téléphone')
                            ->copyable()
                            ->icon('heroicon-o-phone')
                            ->placeholder('Non renseigné'),
                     TextEntry::make('genre')
                ->label('Genre')
                
                ->color(fn (string $state): string => match ($state) {
                    'M', 'Homme' => 'blue',
                    'F', 'Femme' => 'pink',
                    default => 'gray',
                }),
                TextEntry::make('updated_at')
                            ->label('Dernière mise à jour')
                            ->since()
                            ->icon('heroicon-o-clock')
                            ->color('gray'),
                    ]),

                    
                    

                
                ])
                ->columnSpan(9),
            ]),

     
    ]),


                // Section Attribution actuelle
                Section::make('Attribution actuelle')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        ViewEntry::make('attribution_actuelle')
                            ->label('')
                            ->view('filament.infolists.etudiant-attribution')
                    ]),

                // Section Situation financière
                Section::make('Situation financière')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        ViewEntry::make('situation_financiere')
                            ->label('')
                            ->view('filament.infolists.etudiant-finances')
                    ]),

                // Section Historique des paiements
                Section::make('Historique des paiements récents')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        ViewEntry::make('historique_paiements')
                            ->label('')
                            ->view('filament.infolists.etudiant-paiements')
                    ]),

                // Section Statistiques
                Section::make('Statistiques')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('total_paye')
                                    ->label('Total payé')
                                    ->state(function (Etudiant $record): string {
                                        $total = Paiement::where('etudiant_id', $record->id)->sum('montant');
                                        return number_format($total, 0, ',', ' ') . ' FCFA';
                                    })
                                    ->color('success')
                                    ->weight(FontWeight::Bold),
                                
                                TextEntry::make('nombre_paiements')
                                    ->label('Nombre de paiements')
                                    ->state(function (Etudiant $record): int {
                                        return Paiement::where('etudiant_id', $record->id)->count();
                                    })
                                    ->color('info')
                                    ->weight(FontWeight::Bold),
                                
                                TextEntry::make('dernier_paiement')
                                    ->label('Dernier paiement')
                                    ->state(function (Etudiant $record): string {
                                        $dernierPaiement = Paiement::where('etudiant_id', $record->id)
                                            ->latest('date')
                                            ->first();
                                        return $dernierPaiement ? $dernierPaiement->date->format('d/m/Y') : 'Aucun';
                                    })
                                    ->color('warning'),
                                
                                TextEntry::make('inscrit_depuis')
                                    ->label('Crée depuis')
                                    ->state(function (Etudiant $record): string {
                                        return $record->created_at->format('d/m/Y');
                                    })
                                    ->color('gray'),
                            ]),
                    ]),
            ]);
    }
}