<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Calendario operativo</h2>
                <p class="text-sm text-slate-500">Cirugías y entregas programadas. Los usuarios ven solo sus asignaciones; admins ven todo.</p>
            </div>
        </div>
        <div id="calendar" class="bg-white rounded-xl shadow-sm border border-slate-200 p-2"></div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            const events = @js($eventsJson ? json_decode($eventsJson, true) : []);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                },
                eventClick: function(info) {
                    if (info.event.url) {
                        window.open(info.event.url, '_blank');
                        info.jsEvent.preventDefault();
                    }
                },
                eventDidMount: function(info) {
                    if (info.event.extendedProps.tipo) {
                        info.el.setAttribute('title', info.event.extendedProps.tipo);
                    }
                },
                events: events,
                height: 'auto',
                contentHeight: 'auto',
            });

            calendar.render();
        });
    </script>
</x-filament-panels::page>
