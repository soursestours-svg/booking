<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Forms\Components\AceEditor;

class FormComponentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Регистрация кастомных компонентов форм
        \Filament\Forms\Components\Component::register(
            AceEditor::make('ace-editor')
        );
    }
}
