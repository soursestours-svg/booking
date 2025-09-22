<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Пользователи';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationGroup = 'Пользователи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('Пароль')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('edit'),
                Forms\Components\Select::make('roles')
                    ->label('Роли')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->visible(fn() => auth()->user()->can('manage-roles')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => auth()->check() && auth()->user()->can('manage-users')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn($record) => auth()->check() && auth()->user()->can('view-users')),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->check() && auth()->user()->can('edit-users')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => auth()->check() && auth()->user()->can('delete-users')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->check() && auth()->user()->can('delete-users')),
                ]),
            ]);
    }

    protected static function canManageRoles(): bool
    {
        $user = auth()->user();

        // Проверяем, что пользователь аутентифицирован
        if (!$user) {
            return false;
        }

        return $user->can('manage-roles');
    }

    public static function getRelations(): array
    {
        return static::canManageRoles()
            ? [RolesRelationManager::class]
            : [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        return auth()->user()->can('manage-users');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('manage-users');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
