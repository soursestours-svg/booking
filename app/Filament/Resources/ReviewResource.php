<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->step(1),

                Forms\Components\Textarea::make('comment')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_approved')
                    ->label('Одобрено')
                    ->default(false),

                Forms\Components\Toggle::make('is_visible')
                    ->label('Видимо')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Услуга')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state)),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Комментарий')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->comment),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Одобрено')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Видимо')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлен')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Одобрено'),

                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Видимо'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Review $record) => $record->update([
                        'is_approved' => true,
                        'is_visible' => true,
                    ]))
                    ->hidden(fn (Review $record) => $record->is_approved),

                Tables\Actions\Action::make('hide')
                    ->label('Скрыть')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->action(fn (Review $record) => $record->update(['is_visible' => false]))
                    ->hidden(fn (Review $record) => !$record->is_visible),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Одобрить выбранные')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (\Illuminate\Support\Collection $records) =>
                            $records->each->update(['is_approved' => true, 'is_visible' => true])
                        ),

                    Tables\Actions\BulkAction::make('hide')
                        ->label('Скрыть выбранные')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn (\Illuminate\Support\Collection $records) =>
                            $records->each->update(['is_visible' => false])
                        ),
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
            'index' => Pages\ManageReviews::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['service', 'user', 'booking']);
    }
}
