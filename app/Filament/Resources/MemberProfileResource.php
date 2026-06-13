<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberProfileResource\Pages;
use App\Models\MemberProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberProfileResource extends Resource
{
    protected static ?string $model = MemberProfile::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Membres';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Profil membre';
    protected static ?string $pluralModelLabel = 'Profils membres';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Membre')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Utilisateur')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('mentor_id')
                        ->label('Mentor (parrain)')
                        ->options(fn () => User::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ]),
            ]),
            Forms\Components\Section::make('Entreprise / Projet')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Entreprise'),
                    Forms\Components\TextInput::make('project_name')
                        ->label('Projet'),
                    Forms\Components\TextInput::make('sector')
                        ->label("Secteur d'activité"),
                    Forms\Components\TextInput::make('city')
                        ->label('Ville'),
                ]),
                Forms\Components\Textarea::make('bio')
                    ->label('Biographie')
                    ->rows(3),
            ]),
            Forms\Components\Section::make('Compétences & Besoins')->schema([
                Forms\Components\TagsInput::make('skills_offered')
                    ->label('Compétences proposées')
                    ->placeholder('Ajouter une compétence…'),
                Forms\Components\TagsInput::make('needs_expressed')
                    ->label('Besoins exprimés')
                    ->placeholder('Ajouter un besoin…'),
            ]),
            Forms\Components\Section::make('Réseaux sociaux')->schema([
                Forms\Components\KeyValue::make('social_links')
                    ->label('Liens')
                    ->keyLabel('Réseau')
                    ->valueLabel('URL'),
            ]),
            Forms\Components\Section::make('Adhésion')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('membership_status')
                        ->label('Statut')
                        ->options([
                            'candidate' => 'Candidat',
                            'active'    => 'Actif',
                            'suspended' => 'Suspendu',
                            'excluded'  => 'Exclu',
                            'alumni'    => 'Alumni',
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('membership_expires_at')
                        ->label('Expiration'),
                    Forms\Components\TextInput::make('referral_code')
                        ->label('Code de parrainage')
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),
                ]),
                Forms\Components\Textarea::make('admin_notes')
                    ->label('Notes admin')
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
                Tables\Columns\TextColumn::make('membership_status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'    => 'success',
                        'candidate' => 'info',
                        'suspended' => 'warning',
                        'excluded'  => 'danger',
                        'alumni'    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sector')
                    ->label('Secteur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mentor.name')
                    ->label('Mentor')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('membership_expires_at')
                    ->label('Expire le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('membership_status')
                    ->label('Statut')
                    ->options([
                        'candidate' => 'Candidat',
                        'active'    => 'Actif',
                        'suspended' => 'Suspendu',
                        'excluded'  => 'Exclu',
                        'alumni'    => 'Alumni',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('activate')
                    ->label('Activer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->membership_status !== 'active')
                    ->action(fn ($record) => $record->update([
                        'membership_status' => 'active',
                        'activated_at'      => now(),
                    ])),
                Tables\Actions\Action::make('suspend')
                    ->label('Suspendre')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->membership_status === 'active')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['membership_status' => 'suspended'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index'  => Pages\ListMemberProfiles::route('/'),
            'create' => Pages\CreateMemberProfile::route('/create'),
            'edit'   => Pages\EditMemberProfile::route('/{record}/edit'),
        ];
    }
}
