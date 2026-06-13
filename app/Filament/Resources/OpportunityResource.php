<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Activités';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Opportunité';
    protected static ?string $pluralModelLabel = 'Opportunités';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Opportunité')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options([
                            'tender'     => "Appel d'offres",
                            'mission'    => 'Mission',
                            'internship' => 'Stage',
                            'funding'    => 'Financement',
                            'contest'    => 'Concours',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('sector')
                        ->label('Secteur'),
                ]),
                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->required(),
                Forms\Components\TagsInput::make('target_skills')
                    ->label('Compétences ciblées'),
            ]),
            Forms\Components\Section::make('Publication')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('published_by')
                        ->label('Publié par')
                        ->relationship('publisher', 'name')
                        ->default(fn () => auth()->id())
                        ->required(),
                    Forms\Components\Select::make('partner_company_id')
                        ->label('Entreprise partenaire')
                        ->relationship('partnerCompany', 'name')
                        ->searchable()
                        ->nullable(),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('deadline')
                        ->label('Date limite'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
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
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'tender'     => "Appel d'offres",
                        'mission'    => 'Mission',
                        'internship' => 'Stage',
                        'funding'    => 'Financement',
                        'contest'    => 'Concours',
                    }),
                Tables\Columns\TextColumn::make('sector')
                    ->label('Secteur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Limite')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Candidatures')
                    ->counts('applications'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'tender'     => "Appel d'offres",
                        'mission'    => 'Mission',
                        'internship' => 'Stage',
                        'funding'    => 'Financement',
                        'contest'    => 'Concours',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(fn ($record) => $record->update(['is_active' => ! $record->is_active])),
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
            'index'  => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'edit'   => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }
}
