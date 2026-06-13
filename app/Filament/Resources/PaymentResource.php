<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finances';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Paiement';
    protected static ?string $pluralModelLabel = 'Paiements';

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
            Forms\Components\Section::make('Paiement')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Membre')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('subscription_plan_id')
                        ->label('Plan de cotisation')
                        ->relationship('subscriptionPlan', 'name')
                        ->searchable(),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('method')
                        ->label('Moyen de paiement')
                        ->options([
                            'mvola'      => 'MVola',
                            'holo_money' => 'Holo Money',
                            'cash'       => 'Espèces',
                            'cheque'     => 'Chèque',
                        ])
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Montant (KMF)')
                        ->numeric()
                        ->required()
                        ->suffix('KMF'),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('period_start')
                        ->label('Début période'),
                    Forms\Components\DatePicker::make('period_end')
                        ->label('Fin période'),
                ]),
            ]),
            Forms\Components\Section::make('Détails MVola / Holo Money')
                ->visible(fn (Forms\Get $get) => in_array($get('method'), ['mvola', 'holo_money']))
                ->schema([
                    Forms\Components\TextInput::make('transaction_reference')
                        ->label('Référence de transaction'),
                    Forms\Components\FileUpload::make('screenshot_path')
                        ->label('Capture d\'écran')
                        ->image()
                        ->directory('payment-screenshots'),
                ]),
            Forms\Components\Section::make('Détails Chèque')
                ->visible(fn (Forms\Get $get) => $get('method') === 'cheque')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('cheque_number')
                            ->label('N° de chèque'),
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Banque'),
                        Forms\Components\DatePicker::make('cheque_date')
                            ->label('Date du chèque'),
                    ]),
                ]),
            Forms\Components\Section::make('Validation')->schema([
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'   => 'En attente',
                        'validated' => 'Validé',
                        'rejected'  => 'Refusé',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Membre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('Moyen')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'mvola'      => 'MVola',
                        'holo_money' => 'Holo Money',
                        'cash'       => 'Espèces',
                        'cheque'     => 'Chèque',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->suffix(' KMF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending'   => 'warning',
                        'validated' => 'success',
                        'rejected'  => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending'   => 'En attente',
                        'validated' => 'Validé',
                        'rejected'  => 'Refusé',
                    }),
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Période')
                    ->formatStateUsing(fn ($record) => $record->period_start
                        ? $record->period_start->format('d/m/Y').' → '.$record->period_end?->format('d/m/Y')
                        : '—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Soumis le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'   => 'En attente',
                        'validated' => 'Validé',
                        'rejected'  => 'Refusé',
                    ]),
                Tables\Filters\SelectFilter::make('method')
                    ->label('Moyen de paiement')
                    ->options([
                        'mvola'      => 'MVola',
                        'holo_money' => 'Holo Money',
                        'cash'       => 'Espèces',
                        'cheque'     => 'Chèque',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('validate')
                    ->label('Valider')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'       => 'validated',
                            'validated_by' => auth()->id(),
                            'validated_at' => now(),
                        ]);
                        // Mise à jour du profil membre
                        if ($record->period_end) {
                            $record->user->profile?->update([
                                'membership_status'    => 'active',
                                'membership_expires_at'=> $record->period_end,
                            ]);
                        }
                        Notification::make()->title('Paiement validé')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Motif')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'       => 'rejected',
                            'validated_by' => auth()->id(),
                            'validated_at' => now(),
                            'notes'        => $data['notes'],
                        ]);
                        Notification::make()->title('Paiement refusé')->warning()->send();
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
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
