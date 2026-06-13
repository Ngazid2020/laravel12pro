<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Finances';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Plan de cotisation';
    protected static ?string $pluralModelLabel = 'Plans de cotisation';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label('Montant (KMF)')
                        ->numeric()
                        ->required()
                        ->suffix('KMF'),
                    Forms\Components\Select::make('period')
                        ->label('Périodicité')
                        ->options([
                            'monthly' => 'Mensuelle',
                            'annual'  => 'Annuelle',
                        ])
                        ->required(),
                ]),
                Forms\Components\Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->suffix(' KMF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Période')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'annual' ? 'Annuelle' : 'Mensuelle'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
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
            'index'  => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit'   => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
