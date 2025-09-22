<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <label for="service" class="block text-sm font-medium text-gray-700">Выберите услугу</label>
            <select wire:model.live="selectedServiceId" id="service" name="service" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">-- Выберите услугу --</option>
                @foreach($this->services as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        @if ($selectedServiceId)
            <div
                x-data="{
                    calendar: null,
                    events: @entangle('events'),
                    init() {
                        const calendarEl = this.$refs.calendar;
                        this.calendar = new window.Calendar(calendarEl, {
                            plugins: [window.dayGridPlugin, window.interactionPlugin],
                            initialView: 'dayGridMonth',
                            editable: true,
                            events: this.events,
                            locale: 'ru',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,dayGridWeek'
                            },
                            dateClick: (info) => {
                                this.$wire.onDateClick(info.dateStr);
                            },
                            eventDrop: (info) => {
                                this.$wire.onEventDrop({
                                    id: info.event.id,
                                    start: info.event.startStr
                                });
                            }
                        });
                        this.calendar.render();

                        this.$wire.on('events-updated', (e) => {
                           this.calendar.removeAllEvents();
                           this.calendar.addEventSource(e[0].events);
                        });
                    }
                }"
                wire:ignore
            >
                <div x-ref="calendar"></div>
            </div>
        @else
            <div class="p-4 text-center bg-gray-50 rounded-lg">
                <p class="text-gray-500">Пожалуйста, выберите услугу, чтобы увидеть календарь доступности.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
