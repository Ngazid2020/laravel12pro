<?php

namespace App\Filament\Resources\TrainingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';
    protected static ?string $title = 'Sessions';
    protected static ?string $modelLabel = 'session';
    protected static ?string $pluralModelLabel = 'sessions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dates et lieu')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Début')
                        ->required()
                        ->seconds(false),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Fin')
                        ->required()
                        ->seconds(false)
                        ->after('starts_at'),
                ]),
                Forms\Components\TextInput::make('location')
                    ->label('Lieu (présentiel / hybride)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('meeting_link')
                    ->label('Lien de réunion (en ligne / hybride)')
                    ->url()
                    ->maxLength(500),
            ]),

            Forms\Components\Section::make('Statut et ressources')->schema([
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options([
                        'scheduled'  => 'Planifiée',
                        'ongoing'    => 'En cours',
                        'completed'  => 'Terminée',
                        'cancelled'  => 'Annulée',
                    ])
                    ->required()
                    ->default('scheduled'),
                Forms\Components\Repeater::make('materials')
                    ->label('Supports pédagogiques')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->label('Lien')
                            ->url()
                            ->required(),
                    ])
                    ->addActionLabel('Ajouter un support')
                    ->columns(2)
                    ->collapsible()
                    ->defaultItems(0),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('starts_at')
            ->defaultSort('starts_at')
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lieu')
                    ->default('—')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'scheduled' => 'info',
                        'ongoing'   => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'scheduled' => 'Planifiée',
                        'ongoing'   => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    }),
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->label('Inscrits')
                    ->counts('enrollments')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'scheduled' => 'Planifiée',
                        'ongoing'   => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvelle session'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // Bloquer la suppression si des membres sont inscrits
                        if ($record->enrollments()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Suppression impossible')
                                ->body('Des membres sont inscrits à cette session.')
                                ->persistent()
                                ->send();
                            $this->halt();
                        }
                    }),
            ]);
    }
}