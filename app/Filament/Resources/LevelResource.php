<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LevelResource\Pages;
use App\Models\Level;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Gamification';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Niveau';
    protected static ?string $pluralModelLabel = 'Niveaux';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Niveau')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\ColorPicker::make('badge_color')
                        ->label('Couleur du badge'),
                    Forms\Components\TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),
                ]),
            ]),
            Forms\Components\Section::make('Conditions d\'accès (toutes requises)')
                ->description('Un adhérent doit satisfaire TOUTES les conditions pour atteindre ce niveau.')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('min_points')
                            ->label('Points minimum')
                            ->numeric()
                            ->default(0)
                            ->suffix('pts'),
                        Forms\Components\TextInput::make('required_trainings')
                            ->label('Formations suivies min.')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('required_months')
                            ->label("Ancienneté min. (mois)")
                            ->numeric()
                            ->default(0)
                            ->suffix('mois'),
                    ]),
                    Forms\Components\Toggle::make('grants_mentor_status')
                        ->label('Ce niveau accorde le statut Mentor'),
                ]),
            Forms\Components\Section::make('Récompenses')->schema([
                Forms\Components\Repeater::make('rewards')
                    ->relationship()
                    ->label('Récompenses associées')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'badge'                => 'Badge',
                                'premium_training'     => 'Formation premium offerte',
                                'priority_opportunity' => 'Accès prioritaire opportunités',
                                'event_invitation'     => 'Invitation événement partenaire',
                                'referral_bonus'       => 'Prime de parrainage (fixe/unique)',
                                'commission_rate'      => "Taux de commission (affaires conclues)",
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->label('Valeur (KMF ou %)')
                            ->placeholder('ex. 5000 ou 5%'),
                    ])
                    ->columns(3)
                    ->addActionLabel('Ajouter une récompense'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\ColorColumn::make('badge_color')
                    ->label('Couleur'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Niveau')
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_points')
                    ->label('Points min.')
                    ->suffix(' pts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('required_trainings')
                    ->label('Formations')
                    ->suffix(' requises'),
                Tables\Columns\TextColumn::make('required_months')
                    ->label('Ancienneté')
                    ->suffix(' mois'),
                Tables\Columns\IconColumn::make('grants_mentor_status')
                    ->label('→ Mentor')
                    ->boolean(),
                Tables\Columns\TextColumn::make('rewards_count')
                    ->label('Récompenses')
                    ->counts('rewards'),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLevels::route('/'),
            'create' => Pages\CreateLevel::route('/create'),
            'edit'   => Pages\EditLevel::route('/{record}/edit'),
        ];
    }
}
