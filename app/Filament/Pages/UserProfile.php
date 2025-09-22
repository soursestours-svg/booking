<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.user-profile';
    protected static ?string $navigationLabel = 'Мой профиль';
    protected static ?int $navigationSort = -1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Аватар')
                            ->directory('user-avatars')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Section::make('Безопасность')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Текущий пароль')
                            ->password()
                            ->revealable()
                            ->rules(['required_with:new_password'])
                            ->currentPassword(),

                        TextInput::make('new_password')
                            ->label('Новый пароль')
                            ->password()
                            ->revealable()
                            ->rules(['confirmed', 'min:8'])
                            ->autocomplete('new-password'),

                        TextInput::make('new_password_confirmation')
                            ->label('Подтверждение пароля')
                            ->password()
                            ->revealable()
                            ->rules(['min:8'])
                            ->autocomplete('new-password'),
                    ]),
            ])
            ->statePath('data')
            ->model(Auth::user());
    }

    public function save(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        // Логируем данные для отладки
        logger()->debug('Profile save data:', $data);

        // Обрабатываем загрузку аватара
        if (isset($data['avatar'])) {
            // Filament возвращает массив с путями, берем первый элемент
            if (is_array($data['avatar']) && !empty($data['avatar'])) {
                $data['avatar'] = $data['avatar'][0];
            }
        }

        if (!empty($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
        } else {
            // Убираем поля пароля, если они не заполнены
            unset($data['current_password']);
            unset($data['new_password']);
            unset($data['new_password_confirmation']);
        }

        $user->update($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Профиль успешно обновлен',
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Forms\Components\Actions\Action::make('save')
                ->label('Сохранить изменения')
                ->submit('save'),
        ];
    }
}
