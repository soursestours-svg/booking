<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Бронирования';
    protected static ?string $modelLabel = 'бронирование';
    protected static ?string $pluralModelLabel = 'Бронирования';
    protected static ?string $navigationGroup = 'Управление бронированиями';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Услуга'),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Пользователь'),

                Forms\Components\DateTimePicker::make('start_date')
                    ->required()
                    ->label('Дата начала')
                    ->native(false),

                Forms\Components\DateTimePicker::make('end_date')
                    ->required()
                    ->label('Дата окончания')
                    ->native(false),

                Forms\Components\TextInput::make('guests_count')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->label('Количество гостей'),

                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('₽')
                    ->label('Общая стоимость'),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Ожидание',
                        'confirmed' => 'Подтверждено',
                        'cancelled' => 'Отменено',
                        'completed' => 'Завершено',
                    ])
                    ->required()
                    ->default('pending')
                    ->label('Статус'),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->label('Примечания')
                    ->maxLength(500),

                Forms\Components\TextInput::make('customer_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Имя клиента'),

                Forms\Components\TextInput::make('customer_email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Email клиента'),

                Forms\Components\TextInput::make('customer_phone')
                    ->tel()
                    ->maxLength(255)
                    ->label('Телефон клиента'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable()
                    ->sortable()
                    ->label('Услуга'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable()
                    ->label('Клиент'),

                Tables\Columns\TextColumn::make('customer_email')
                    ->searchable()
                    ->label('Email'),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->searchable()
                    ->label('Телефон'),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('Начало'),

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('Окончание'),

                Tables\Columns\TextColumn::make('guests_count')
                    ->numeric()
                    ->sortable()
                    ->label('Гости'),

                Tables\Columns\TextColumn::make('total_price')
                    ->money('RUB')
                    ->sortable()
                    ->label('Стоимость'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидание',
                        'confirmed' => 'Подтверждено',
                        'cancelled' => 'Отменено',
                        'completed' => 'Завершено',
                    })
                    ->label('Статус'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Создано'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Ожидание',
                        'confirmed' => 'Подтверждено',
                        'cancelled' => 'Отменено',
                        'completed' => 'Завершено',
                    ])
                    ->label('Статус'),

                Tables\Filters\SelectFilter::make('service_id')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Услуга'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->action(fn (Booking $record) => $record->update(['status' => 'confirmed']))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->hidden(fn (Booking $record) => $record->status !== 'pending'),

                Tables\Actions\Action::make('cancel')
                    ->action(fn (Booking $record) => $record->update(['status' => 'cancelled']))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->hidden(fn (Booking $record) => $record->status === 'cancelled'),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->when($user->hasRole('partner') && !$user->hasRole('admin'), function ($query) use ($user) {
                return $query->whereHas('service', function ($q) use ($user) {
                    $q->where('partner_id', $user->id);
                });
            });
    }
}
