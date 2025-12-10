<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-semibold text-slate-800">Calendario operativo</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ \App\Filament\Resources\Cirugias\CirugiaResource::getUrl('create') }}" class="cal-btn">Crear cirugía</a>
                    <a href="{{ \App\Filament\Resources\Pedidos\PedidoResource::getUrl('create') }}" class="cal-btn">Crear pedido</a>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <label class="cal-chip">
                    <input type="checkbox" id="filter-cirugias" checked class="accent-sky-500">
                    Cirugías
                </label>
                <label class="cal-chip">
                    <input type="checkbox" id="filter-pedidos" checked class="accent-amber-500">
                    Pedidos
                </label>
                <details class="cal-legend">
                    <summary>Ver leyenda</summary>
                    <div class="cal-legend-grid">
                        <div class="cal-legend-item"><span class="cal-dot" style="background:#0ea5e9"></span><span>Pendiente / Solicitado</span></div>
                        <div class="cal-legend-item"><span class="cal-dot" style="background:#2563eb"></span><span>En curso</span></div>
                        <div class="cal-legend-item"><span class="cal-dot" style="background:#22c55e"></span><span>Cerrada / Entregado</span></div>
                        <div class="cal-legend-item"><span class="cal-dot" style="background:#f59e0b"></span><span>Reprogramada / Preparación</span></div>
                        <div class="cal-legend-item"><span class="cal-dot" style="background:#ef4444"></span><span>Observado</span></div>
                        <div class="cal-legend-item"><span class="cal-dot" style="background:#9ca3af"></span><span>Cancelada / Devuelto</span></div>
                        <div class="cal-legend-item"><span class="cal-dot" style="background:#6366f1"></span><span>Despachado</span></div>
                    </div>
                </details>
            </div>
        </div>
        <div id="calendar" class="bg-white rounded-xl shadow-sm border border-slate-200 p-2"></div>
    </div>

    <style>
        .cal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            padding: 0 14px;
            border-radius: 999px;
            background: #0dbdf0;
            color: #0b2f3b;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }
        .cal-btn:hover {
            background: #0cc0f5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        .cal-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
            cursor: pointer;
        }
        .cal-chip input {
            margin: 0;
        }
        .cal-legend {
            font-size: 12px;
            color: #0f172a;
            cursor: pointer;
        }
        .cal-legend summary {
            list-style: none;
            outline: none;
        }
        .cal-legend[open] summary {
            margin-bottom: 6px;
        }
        .cal-legend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 6px 12px;
        }
        .cal-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 6px;
            background: #f8fafc;
            border-radius: 10px;
        }
        .cal-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
        }
    </style>

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
                    const tipo = (ev.extendedProps?.tipo || '').toString().toLowerCase();
                    const isCir = tipo.includes('cirug');
                    const isPed = tipo.includes('pedido');
                    if (isCir) return showCir;
                    if (isPed) return showPed;
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
