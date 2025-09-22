<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceAvailabilityResource\Pages;
use App\Models\ServiceAvailability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceAvailabilityResource extends Resource
{
    protected static ?string $model = ServiceAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Доступность услуг';
    protected static ?string $pluralModelLabel = 'Доступность услуг';
    protected static ?string $navigationGroup = 'Управление';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Дата')
                    ->required()
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->minDate(now()),

                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->minValue(0)
                    ->prefix('₽'),

                Forms\Components\TextInput::make('available_slots')
                    ->label('Доступные слоты')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),

                Forms\Components\Toggle::make('is_available')
                    ->label('Доступно для бронирования')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Услуга')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_slots')
                    ->label('Доступные слоты')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Доступно')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'name')
                    ->label('Услуга')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('От даты'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('До даты'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Доступность'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceAvailabilities::route('/'),
            'create' => Pages\CreateServiceAvailability::route('/create'),
            'edit' => Pages\EditServiceAvailability::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
