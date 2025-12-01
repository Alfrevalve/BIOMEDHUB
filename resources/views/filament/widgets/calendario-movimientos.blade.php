<x-filament-widgets::widget>
    <x-filament::card>
        {{-- 
            SOLUCIÓN: Envolvemos todo en este DIV para asegurar
            que Livewire vea un solo bloque de contenido.
        --}}
        <div>
            {{-- Estilos CSS --}}
            <style>
                .fc .fc-button-primary {
                    background-color: rgb(245, 158, 11);
                    border-color: rgb(245, 158, 11);
                    font-weight: 600;
                }
                .fc .fc-button-primary:hover {
                    background-color: rgb(217, 119, 6);
                    border-color: rgb(217, 119, 6);
                }
                .fc .fc-button-primary:disabled {
                    background-color: #d1d5db;
                    border-color: #d1d5db;
                }
                .fc .fc-day-today {
                    background-color: rgba(245, 158, 11, 0.1) !important;
                }
                .fc-event {
                    cursor: pointer;
                    border-radius: 4px;
                    font-size: 0.85em;
                }
                .fc .fc-button:focus {
                    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.5);
                }
            </style>

            {{-- Título --}}
            <h2 class="text-xl font-bold mb-4">
                Calendario de Movimientos y Cirugías
            </h2>

            {{-- Área del Calendario --}}
            <div wire:ignore>
                <div 
                    x-data="biomedCalendarWidget({ events: @js($events) })"
                    class="min-h-[650px]"
                >
                    {{-- Loader --}}
                    <div x-show="!loaded" class="flex items-center justify-center h-64 text-gray-500">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Cargando librerías del calendario...</span>
                    </div>

                    {{-- El Calendario en sí --}}
                    <div x-ref="calendar" x-show="loaded"></div>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>

@assets
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
@endassets

@script
<script>
    Alpine.data('biomedCalendarWidget', ({ events }) => ({
        calendar: null,
        loaded: false,
        filterType: 'all', 

        init() {
            if (typeof FullCalendar === 'undefined') {
                this.loadLibrary();
            } else {
                this.mountCalendar();
            }
        },

        loadLibrary() {
            if (!document.getElementById('fullcalendar-css')) {
                const link = document.createElement('link');
                link.id = 'fullcalendar-css';
                link.href = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css';
                link.rel = 'stylesheet';
                document.head.appendChild(link);
            }

            if (!document.getElementById('fullcalendar-js')) {
                const script = document.createElement('script');
                script.id = 'fullcalendar-js';
                script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js';
                script.onload = () => {
                    this.mountCalendar();
                };
                script.onerror = () => {
                    console.error('Error al cargar FullCalendar');
                };
                document.head.appendChild(script);
            } else {
                setTimeout(() => this.init(), 100);
            }
        },

        getFilteredEvents() {
            if (this.filterType === 'all') return events;
            return events.filter(ev => ev.type === this.filterType);
        },

        mountCalendar() {
            if (typeof FullCalendar === 'undefined') {
                setTimeout(() => this.mountCalendar(), 100);
                return;
            }

            const calendarEl = this.$refs.calendar;

            if (this.calendar) {
                this.calendar.destroy();
            }

            const self = this;

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                firstDay: 1,
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
                },
                events: self.getFilteredEvents(),
                eventClick(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                eventDidMount(info) {
                    if (info.event.extendedProps.tooltip) {
                        info.el.title = info.event.extendedProps.tooltip;
                    }
                },
            });

            this.calendar.render();
            this.loaded = true;
        },
    }));
</script>
@endscript