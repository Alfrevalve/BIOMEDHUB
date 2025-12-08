<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Calendario operativo</h2>
                <p class="text-sm text-slate-500">Cirugías y entregas programadas. Los usuarios ven solo sus asignaciones; admins ven todo.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs items-center">
                <label class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700 cursor-pointer">
                    <input type="checkbox" id="filter-cirugias" checked class="accent-sky-500">
                    Cirugías
                </label>
                <label class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700 cursor-pointer">
                    <input type="checkbox" id="filter-pedidos" checked class="accent-amber-500">
                    Pedidos
                </label>
                <div class="flex flex-wrap gap-2">
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700"><span class="w-3 h-3 rounded-full" style="background:#0ea5e9"></span>Pendiente/Solicitado</div>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700"><span class="w-3 h-3 rounded-full" style="background:#2563eb"></span>En curso</div>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700"><span class="w-3 h-3 rounded-full" style="background:#22c55e"></span>Cerrada/Entregado</div>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700"><span class="w-3 h-3 rounded-full" style="background:#f59e0b"></span>Reprogramada/Preparación</div>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700"><span class="w-3 h-3 rounded-full" style="background:#ef4444"></span>Observado</div>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700"><span class="w-3 h-3 rounded-full" style="background:#9ca3af"></span>Cancelada/Devuelto</div>
                </div>
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

            let filteredEvents = events;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
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
                events: filteredEvents,
                height: 'auto',
                contentHeight: 'auto',
            });

            calendar.render();

            const applyFilters = () => {
                const showCir = document.getElementById('filter-cirugias')?.checked ?? true;
                const showPed = document.getElementById('filter-pedidos')?.checked ?? true;
                filteredEvents = events.filter(ev => {
                    if (ev.extendedProps?.tipo === 'Cirugía') return showCir;
                    if (ev.extendedProps?.tipo === 'Pedido') return showPed;
                    return true;
                });
                calendar.removeAllEventSources();
                calendar.addEventSource(filteredEvents);
            };

            document.getElementById('filter-cirugias')?.addEventListener('change', applyFilters);
            document.getElementById('filter-pedidos')?.addEventListener('change', applyFilters);
        });
    </script>
</x-filament-panels::page>
