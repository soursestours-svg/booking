<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Услуги';
    protected static ?string $modelLabel = 'услугу';
    protected static ?string $pluralModelLabel = 'Услуги';
    protected static ?string $navigationGroup = 'Управление контентом';

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isPartner = $user->hasRole('partner') && !$user->hasRole('admin');

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Название услуги'),

                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->label('Описание')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'blockquote', 'codeBlock', 'h2', 'h3',
                        'bulletList', 'orderedList', 'link',
                    ]),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('₽')
                    ->label('Цена'),

                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->multiple()
                    ->reorderable()
                    ->imageEditor()
                    ->label('Изображения')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Активна'),

                Forms\Components\Select::make('partner_id')
                    ->relationship('partner', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Партнер')
                    ->hidden($isPartner)
                    ->default($isPartner ? $user->id : null)
                    ->disabled($isPartner),

                Forms\Components\Select::make('type')
                    ->options([
                        'standard' => 'Стандартная',
                        'premium' => 'Премиум',
                        'vip' => 'VIP',
                    ])
                    ->required()
                    ->default('standard')
                    ->label('Тип услуги'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isPartner = $user->hasRole('partner') && !$user->hasRole('admin');

        $columns = [
            SpatieMediaLibraryImageColumn::make('images')
                ->collection('images')
                ->label(''),

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Название'),

            Tables\Columns\TextColumn::make('price')
                ->money('RUB')
                ->sortable()
                ->label('Цена'),
        ];

        // Показываем колонку партнера только администраторам
        if (!$isPartner) {
            $columns[] = Tables\Columns\TextColumn::make('partner.name')
                ->searchable()
                ->sortable()
                ->label('Партнер');
        }

        $columns = array_merge($columns, [
            Tables\Columns\TextColumn::make('type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'standard' => 'gray',
                    'premium' => 'warning',
                    'vip' => 'success',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'standard' => 'Стандартная',
                    'premium' => 'Премиум',
                    'vip' => 'VIP',
                })
                ->label('Тип'),

            Tables\Columns\IconColumn::make('is_active')
                ->boolean()
                ->label('Активна')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d.m.Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Создана'),
        ]);

        $filters = [
            Tables\Filters\SelectFilter::make('type')
                ->options([
                    'standard' => 'Стандартная',
                    'premium' => 'Премиум',
                    'vip' => 'VIP',
                ])
                ->label('Тип услуги'),

            Tables\Filters\TernaryFilter::make('is_active')
                ->label('Активность'),
        ];

        // Фильтр по партнеру только для администраторов
        if (!$isPartner) {
            $filters[] = Tables\Filters\SelectFilter::make('partner_id')
                ->relationship('partner', 'name')
                ->searchable()
                ->preload()
                ->label('Партнер');
        }

        return $table
            ->columns($columns)
            ->filters($filters)
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->when($user->hasRole('partner') && !$user->hasRole('admin'), function ($query) use ($user) {
                return $query->where('partner_id', $user->id);
            });
    }
}
