<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $modelLabel = 'Разрешение';
    protected static ?string $pluralModelLabel = 'Разрешения';
    protected static ?string $navigationGroup = 'Пользователи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Название')
                    ->unique(ignoreRecord: true),

                Forms\Components\Toggle::make('is_active')
                    ->label('Активно')
                    ->default(true),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Роли'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активно')
                    ->boolean(),

                Tables\Columns\TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label('Кол-во ролей'),
            ])
            ->filters([
                // Удален TrashedFilter, так как модель не использует мягкое удаление
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Удалены ForceDeleteBulkAction и RestoreBulkAction
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
        // Удален withoutGlobalScopes, так как модель не использует SoftDeletingScope
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        return auth()->user()->can('manage-permissions');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('manage-permissions');
    }

}
