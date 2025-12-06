<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Notificaciones</x-slot>
        {{ $this->table->render() }}
    </x-filament::section>
</x-filament-panels::page>
