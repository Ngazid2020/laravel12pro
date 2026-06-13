<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecommendationResource\Pages;
use App\Models\Recommendation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecommendationResource extends Resource
{
    protected static ?string $model = Recommendation::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationGroup = 'Réseau';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Recommandation';
    protected static ?string $pluralModelLabel = 'Recommandations';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Demande')->schema([
                Forms\Components\Select::make('requester_id')
                    ->label('Demandeur')
                    ->relationship('requester', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('need_description')
                    ->label('Description du besoin')
                    ->rows(4)
                    ->required(),
            ]),
            Forms\Components\Section::make('Cible')->schema([
                Forms\Components\Select::make('partner_company_id')
                    ->label('Entreprise partenaire')
                    ->relationship('partnerCompany', 'name')
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('target_user_id')
                    ->label('Ou — Membre cible')
                    ->relationship('targetUser', 'name')
                    ->searchable()
                    ->nullable(),
            ])->description('Remplir l\'un ou l\'autre (pas les deux).'),
            Forms\Components\Section::make('Suivi')->schema([
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'          => 'En attente',
                        'examining'        => 'En examen',
                        'transmitted'      => 'Transmise',
                        'meeting_obtained' => 'RDV obtenu',
                        'deal_closed'      => 'Affaire conclue',
                        'refused'          => 'Refusée',
                    ]),
                Forms\Components\TextInput::make('estimated_value')
                    ->label('Valeur estimée (KMF)')
                    ->numeric()
                    ->suffix('KMF'),
                Forms\Components\Textarea::make('outcome_notes')
                    ->label('Notes de résultat')
                    ->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Demandeur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partnerCompany.name')
                    ->label('Partenaire')
                    ->placeholder('— membre —'),
                Tables\Columns\TextColumn::make('need_description')
                    ->label('Besoin')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending'          => 'gray',
                        'examining'        => 'info',
                        'transmitted'      => 'warning',
                        'meeting_obtained' => 'primary',
                        'deal_closed'      => 'success',
                        'refused'          => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending'          => 'En attente',
                        'examining'        => 'En examen',
                        'transmitted'      => 'Transmise',
                        'meeting_obtained' => 'RDV obtenu',
                        'deal_closed'      => 'Affaire conclue',
                        'refused'          => 'Refusée',
                    }),
                Tables\Columns\TextColumn::make('estimated_value')
                    ->label('Valeur')
                    ->suffix(' KMF')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'          => 'En attente',
                        'examining'        => 'En examen',
                        'transmitted'      => 'Transmise',
                        'meeting_obtained' => 'RDV obtenu',
                        'deal_closed'      => 'Affaire conclue',
                        'refused'          => 'Refusée',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('examine')
                    ->label('Examiner')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update([
                        'status'      => 'examining',
                        'examined_by' => auth()->id(),
                        'examined_at' => now(),
                    ])),
                Tables\Actions\Action::make('transmit')
                    ->label('Transmettre')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'examining')
                    ->action(fn ($record) => $record->update([
                        'status'         => 'transmitted',
                        'transmitted_at' => now(),
                    ])),
                Tables\Actions\Action::make('close')
                    ->label('Clôturer')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['transmitted', 'meeting_obtained']))
                    ->form([
                        Forms\Components\Select::make('outcome')
                            ->label('Résultat')
                            ->options([
                                'meeting_obtained' => 'RDV obtenu',
                                'deal_closed'      => 'Affaire conclue',
                                'refused'          => 'Refusée',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('estimated_value')
                            ->label('Valeur estimée (KMF)')
                            ->numeric()
                            ->suffix('KMF'),
                        Forms\Components\Textarea::make('outcome_notes')
                            ->label('Notes')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'          => $data['outcome'],
                            'estimated_value' => $data['estimated_value'] ?? null,
                            'outcome_notes'   => $data['outcome_notes'] ?? null,
                        ]);
                        Notification::make()->title('Recommandation mise à jour')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRecommendations::route('/'),
            'create' => Pages\CreateRecommendation::route('/create'),
            'edit'   => Pages\EditRecommendation::route('/{record}/edit'),
        ];
    }
}
