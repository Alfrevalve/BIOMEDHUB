<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Auditoría del sistema</x-slot>
        {{ $this->table->render() }}
    </x-filament::section>
</x-filament-panels::page>
