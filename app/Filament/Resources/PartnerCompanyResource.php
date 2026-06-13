<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerCompanyResource\Pages;
use App\Models\PartnerCompany;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerCompanyResource extends Resource
{
    protected static ?string $model = PartnerCompany::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Réseau';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Entreprise partenaire';
    protected static ?string $pluralModelLabel = 'Entreprises partenaires';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identité')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('sector')
                        ->label("Secteur d'activité"),
                ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\FileUpload::make('logo')
                        ->label('Logo')
                        ->image()
                        ->directory('partner-logos'),
                    Forms\Components\TextInput::make('website')
                        ->label('Site web')
                        ->url(),
                ]),
            ]),
            Forms\Components\Section::make('Contact')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('contact_name')
                        ->label('Nom du contact'),
                    Forms\Components\TextInput::make('contact_email')
                        ->label('Email')
                        ->email(),
                    Forms\Components\TextInput::make('contact_phone')
                        ->label('Téléphone')
                        ->tel(),
                ]),
            ]),
            Forms\Components\Section::make('Visibilité')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                    Forms\Components\Toggle::make('show_publicly')
                        ->label('Visible sur la vitrine publique')
                        ->default(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sector')
                    ->label('Secteur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Email contact')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('show_publicly')
                    ->label('Public')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
                Tables\Filters\TernaryFilter::make('show_publicly')
                    ->label('Visible publiquement'),
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
            'index'  => Pages\ListPartnerCompanies::route('/'),
            'create' => Pages\CreatePartnerCompany::route('/create'),
            'edit'   => Pages\EditPartnerCompany::route('/{record}/edit'),
        ];
    }
}
