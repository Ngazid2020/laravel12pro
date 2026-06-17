<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidatureApplicationResource\Pages;
use App\Mail\CandidatureAcceptedMail;
use App\Models\CandidatureApplication;
use App\Models\MemberProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class CandidatureApplicationResource extends Resource
{
    protected static ?string $model = CandidatureApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Membres';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Candidature';
    protected static ?string $pluralModelLabel = 'Candidatures';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Candidat')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('motivation')
                    ->label('Lettre de motivation')
                    ->rows(6)
                    ->required(),
                Forms\Components\FileUpload::make('attachments')
                    ->label('Pièces jointes')
                    ->multiple()
                    ->directory('candidatures')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxFiles(5),
            ]),
            Forms\Components\Section::make('Décision admin')->schema([
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'accepted' => 'Acceptée',
                        'rejected' => 'Refusée',
                        'on_hold'  => 'En attente (hold)',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Motif de refus')
                    ->rows(3)
                    ->visible(fn (Forms\Get $get) => $get('status') === 'rejected'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Candidat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending'  => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'on_hold'  => 'info',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending'  => 'En attente',
                        'accepted' => 'Acceptée',
                        'rejected' => 'Refusée',
                        'on_hold'  => 'En suspens',
                    }),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Examinée le')
                    ->date('d/m/Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Soumise le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'accepted' => 'Acceptée',
                        'rejected' => 'Refusée',
                        'on_hold'  => 'En suspens',
                    ]),
            ])
            ->actions([
                ActionGroup::make([

                    Tables\Actions\ViewAction::make(),
                ]),
                Tables\Actions\Action::make('accept')
                    ->label('Accepter')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Accepter la candidature')
                    ->modalDescription('Un compte membre sera activé pour ce candidat.')
                    ->action(function ($record) {
                        $record->update([
                            'status'      => 'accepted',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        // Crée ou active le profil membre
                        MemberProfile::updateOrCreate(
                            ['user_id' => $record->user_id],
                            [
                                'membership_status' => 'active',
                                'activated_at'      => now(),
                            ]
                        );
                        // Générer le lien de définition du mot de passe (valable 60 min)
                        $token    = Password::broker()->createToken($record->user);
                        $setupUrl = route('password.reset', [
                            'token' => $token,
                            'email' => $record->user->email,
                        ]);

                        Mail::to($record->user->email)
                            ->queue(new CandidatureAcceptedMail($record, $setupUrl));
                        Notification::make()
                            ->title('Candidature acceptée — email envoyé')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motif de refus')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'reviewed_by'      => Auth::id(),
                            'reviewed_at'      => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()
                            ->title('Candidature refusée')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\Action::make('hold')
                    ->label('Mettre en suspens')
                    ->icon('heroicon-o-pause')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update([
                        'status'      => 'on_hold',
                        'reviewed_by' => Auth::id(),
                        'reviewed_at' => now(),
                    ])),
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
            'index'  => Pages\ListCandidatureApplications::route('/'),
            'create' => Pages\CreateCandidatureApplication::route('/create'),
            'edit'   => Pages\EditCandidatureApplication::route('/{record}/edit'),
        ];
    }
}
