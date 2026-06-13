<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Activités';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Événement';
    protected static ?string $pluralModelLabel = 'Événements';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Événement')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options([
                            'networking'  => 'Networking',
                            'conference'  => 'Conférence',
                            'masterclass' => 'Masterclass',
                            'workshop'    => 'Atelier',
                        ])
                        ->required(),
                    Forms\Components\Select::make('organizer_id')
                        ->label('Organisateur')
                        ->relationship('organizer', 'name')
                        ->default(fn () => auth()->id())
                        ->required(),
                ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(4),
            ]),
            Forms\Components\Section::make('Date & Lieu')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Début')
                        ->required(),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Fin'),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('location')
                        ->label('Lieu'),
                    Forms\Components\TextInput::make('capacity')
                        ->label('Capacité max.')
                        ->numeric(),
                ]),
            ]),
            Forms\Components\Section::make('Billetterie')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_paid')
                        ->label('Événement payant')
                        ->live(),
                    Forms\Components\TextInput::make('price')
                        ->label('Prix (KMF)')
                        ->numeric()
                        ->suffix('KMF')
                        ->visible(fn (Forms\Get $get) => $get('is_paid')),
                ]),
                Forms\Components\Toggle::make('is_published')
                    ->label('Publié')
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
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'networking'  => 'Networking',
                        'conference'  => 'Conférence',
                        'masterclass' => 'Masterclass',
                        'workshop'    => 'Atelier',
                    }),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lieu')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Inscrits')
                    ->counts('registrations'),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacité')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'networking'  => 'Networking',
                        'conference'  => 'Conférence',
                        'masterclass' => 'Masterclass',
                        'workshop'    => 'Atelier',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publié'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_published')
                    ->label(fn ($record) => $record->is_published ? 'Dépublier' : 'Publier')
                    ->icon(fn ($record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(fn ($record) => $record->update(['is_published' => ! $record->is_published])),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
