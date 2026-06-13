<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Membres';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identité')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->label('Prénom')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('last_name')
                        ->label('Nom')
                        ->maxLength(100),
                ]),
                Forms\Components\TextInput::make('name')
                    ->label('Nom d\'affichage')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(30),
                ]),
            ]),
            Forms\Components\Section::make('Sécurité')->schema([
                Forms\Components\TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->confirmed(),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirmation')
                    ->password()
                    ->dehydrated(false),
            ]),
            Forms\Components\Section::make('Rôles')->schema([
                Forms\Components\Select::make('roles')
                    ->label('Rôles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('profile.membership_status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'active'    => 'success',
                        'candidate' => 'info',
                        'suspended' => 'warning',
                        'excluded'  => 'danger',
                        'alumni'    => 'gray',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôles')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('membership_status')
                    ->label('Statut adhésion')
                    ->relationship('profile', 'membership_status')
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
