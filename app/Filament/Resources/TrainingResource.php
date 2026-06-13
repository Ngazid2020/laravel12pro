<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Models\Training;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Formations';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Formation';
    protected static ?string $pluralModelLabel = 'Formations';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Formation')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(4),
                Forms\Components\Select::make('trainer_id')
                    ->label('Formateur')
                    ->relationship('trainer', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('prerequisites')
                    ->label('Prérequis')
                    ->rows(2),
            ]),
            Forms\Components\Section::make('Format & Tarif')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('format')
                        ->label('Format')
                        ->options([
                            'in_person' => 'Présentiel',
                            'online'    => 'En ligne',
                            'hybrid'    => 'Hybride',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('capacity')
                        ->label('Capacité max.')
                        ->numeric(),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('price_type')
                        ->label('Type de tarif')
                        ->options([
                            'free'     => 'Gratuit',
                            'included' => 'Inclus cotisation',
                            'premium'  => 'Payant',
                        ])
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('price')
                        ->label('Prix (KMF)')
                        ->numeric()
                        ->suffix('KMF')
                        ->visible(fn (Forms\Get $get) => $get('price_type') === 'premium'),
                ]),
                Forms\Components\Toggle::make('is_published')
                    ->label('Publié dans le catalogue')
                    ->default(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trainer.name')
                    ->label('Formateur'),
                Tables\Columns\TextColumn::make('format')
                    ->label('Format')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'in_person' => 'Présentiel',
                        'online'    => 'En ligne',
                        'hybrid'    => 'Hybride',
                    }),
                Tables\Columns\TextColumn::make('price_type')
                    ->label('Tarif')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'free'     => 'success',
                        'included' => 'info',
                        'premium'  => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'free'     => 'Gratuit',
                        'included' => 'Inclus',
                        'premium'  => 'Payant',
                    }),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sessions_count')
                    ->label('Sessions')
                    ->counts('sessions'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('format')
                    ->label('Format')
                    ->options(['in_person' => 'Présentiel', 'online' => 'En ligne', 'hybrid' => 'Hybride']),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publié'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_published')
                    ->label(fn ($record) => $record->is_published ? 'Dépublier' : 'Publier')
                    ->icon(fn ($record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(fn ($record) => $record->update(['is_published' => ! $record->is_published])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit'   => Pages\EditTraining::route('/{record}/edit'),
        ];
    }
}
