<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MentoringRequestResource\Pages;
use App\Models\MemberProfile;
use App\Models\MentoringRequest;
use App\Notifications\MentoringRequestReviewed;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MentoringRequestResource extends Resource
{
    protected static ?string $model = MentoringRequest::class;

    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Demandes mentorat';
    protected static ?string $modelLabel      = 'Demande de mentorat';
    protected static ?string $pluralModelLabel = 'Demandes de mentorat';
    protected static ?string $navigationGroup = 'Membres';
    protected static ?int    $navigationSort  = 5;

    public static function table(Table $table): Table
    {
        return $table
            ->query(MentoringRequest::with(['requester', 'mentor']))
            ->columns([
                TextColumn::make('requester.name')
                    ->label('Demandeur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mentor.name')
                    ->label('Mentor demandé')
                    ->searchable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'  => 'En attente',
                        'approved' => 'Approuvée',
                        'rejected' => 'Refusée',
                        default    => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'approved' => 'Approuvées',
                        'rejected' => 'Refusées',
                    ]),
            ])
            ->actions([
                Action::make('approuver')
                    ->label('Approuver')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (MentoringRequest $r): bool => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Approuver la demande de mentorat')
                    ->modalDescription(fn (MentoringRequest $r): string =>
                        $r->requester->name.' sera assigné à '.$r->mentor->name.' comme mentor.'
                    )
                    ->action(function (MentoringRequest $r): void {
                        // Assigner le mentor sur le profil du demandeur
                        MemberProfile::where('user_id', $r->requester_id)
                            ->update(['mentor_id' => $r->mentor_id]);

                        // Approuver cette demande
                        $r->update(['status' => 'approved', 'reviewed_at' => now()]);

                        // Annuler les autres demandes en attente du même demandeur
                        MentoringRequest::where('requester_id', $r->requester_id)
                            ->where('id', '!=', $r->id)
                            ->where('status', 'pending')
                            ->delete();

                        // Notifier le membre
                        $r->requester->notify(new MentoringRequestReviewed($r));
                    }),

                Action::make('rejeter')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (MentoringRequest $r): bool => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Rejeter la demande de mentorat')
                    ->modalDescription(fn (MentoringRequest $r): string =>
                        $r->requester->name.' sera notifié du refus et pourra soumettre une nouvelle demande.'
                    )
                    ->action(function (MentoringRequest $r): void {
                        $r->update(['status' => 'rejected', 'reviewed_at' => now()]);
                        $r->requester->notify(new MentoringRequestReviewed($r));
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMentoringRequests::route('/'),
        ];
    }
}
