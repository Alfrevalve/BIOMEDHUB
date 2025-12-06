<x-filament::section>
    <x-slot name="heading">Reservas de materiales</x-slot>
    @if($reservas->isEmpty())
        <div class="text-gray-500 text-sm">Sin reservas asociadas.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($reservas as $reserva)
                <div class="border rounded-lg p-3 bg-white/5">
                    <div class="text-sm font-semibold">{{ $reserva->item->nombre ?? 'Item' }} ({{ $reserva->cantidad }})</div>
                    <div class="text-xs text-gray-400">Estado: {{ $reserva->estado }}</div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament::section>
