<?php

namespace App\Filament\Pages;

use App\Models\Service;
use App\Models\ServiceAvailability;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class PartnerAvailabilityCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static string $view = 'filament.pages.partner-availability-calendar';
    protected static ?string $navigationGroup = 'Услуги';
    protected static ?string $navigationLabel = 'Календарь доступности';
    protected static ?int $navigationSort = 2;

    public ?int $selectedServiceId = null;
    public array $events = [];

    public function mount(): void
    {
        $this->loadEvents();
    }

    public function updatedSelectedServiceId($serviceId): void
    {
        $this->selectedServiceId = $serviceId;
        $this->loadEvents();
        $this->dispatch('events-updated', ['events' => $this->events]);
    }

    public function loadEvents(): void
    {
        if (!$this->selectedServiceId) {
            $this->events = [];
            return;
        }

        $availabilities = ServiceAvailability::where('service_id', $this->selectedServiceId)->get();

        $this->events = $availabilities->map(function (ServiceAvailability $availability) {
            return [
                'id' => $availability->id,
                'title' => "Цена: {$availability->price} ₽, Слоты: {$availability->available_slots}",
                'start' => $availability->date,
                'allDay' => true,
                'extendedProps' => [
                    'price' => $availability->price,
                    'available_slots' => $availability->available_slots
                ]
            ];
        })->toArray();
    }

    public function onEventDrop($event): void
    {
        $availability = ServiceAvailability::find($event['id']);
        if ($availability) {
            $availability->update(['date' => $event['start']]);
            $this->loadEvents();
            Notification::make()->success()->title('Дата доступности обновлена.')->send();
        }
    }

    public function onDateClick($date): void
    {
        if (!$this->selectedServiceId) {
            Notification::make()->warning()->title('Пожалуйста, сначала выберите услугу.')->send();
            return;
        }

        // Здесь можно открыть модальное окно для добавления цены и слотов
        // Для простоты пока создаем с дефолтными значениями
        ServiceAvailability::create([
            'service_id' => $this->selectedServiceId,
            'date' => $date,
            'price' => Service::find($this->selectedServiceId)->price, // Берем базовую цену услуги
            'available_slots' => 10, // Дефолтное значение
            'is_available' => true,
        ]);

        $this->loadEvents();
        $this->dispatch('events-updated', ['events' => $this->events]);
        Notification::make()->success()->title('Доступность на ' . $date . ' добавлена.')->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Можно добавить виджеты, если нужно
        ];
    }

    public function getServicesProperty()
    {
        return Service::where('partner_id', Auth::id())->pluck('name', 'id');
    }

    public static function canView(): bool
    {
        return Auth::user()->hasRole('partner');
    }
}
