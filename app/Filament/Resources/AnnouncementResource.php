<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Annonce';
    protected static ?string $pluralModelLabel = 'Annonces';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Annonce')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('content')
                    ->label('Contenu')
                    ->required(),
            ]),
            Forms\Components\Section::make('Publication')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('target_audience')
                        ->label('Audience cible')
                        ->options([
                            'all'        => 'Tous les membres',
                            'active'     => 'Membres actifs uniquement',
                            'candidates' => 'Candidats uniquement',
                        ])
                        ->required()
                        ->default('all'),
                    Forms\Components\Select::make('published_by')
                        ->label('Publié par')
                        ->relationship('publisher', 'name')
                        ->default(fn () => auth()->id())
                        ->required(),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Date de publication')
                        ->helperText('Laisser vide pour brouillon.'),
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label("Date d'expiration")
                        ->helperText('Laisser vide pour ne pas expirer.'),
                ]),
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
                Tables\Columns\TextColumn::make('target_audience')
                    ->label('Audience')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'all'        => 'Tous',
                        'active'     => 'Actifs',
                        'candidates' => 'Candidats',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publiée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Brouillon'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('target_audience')
                    ->label('Audience')
                    ->options([
                        'all'        => 'Tous',
                        'active'     => 'Actifs',
                        'candidates' => 'Candidats',
                    ]),
                Tables\Filters\Filter::make('published')
                    ->label('Publiées')
                    ->query(fn ($query) => $query->whereNotNull('published_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish_now')
                    ->label('Publier maintenant')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => $record->published_at === null)
                    ->action(fn ($record) => $record->update(['published_at' => now()])),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
