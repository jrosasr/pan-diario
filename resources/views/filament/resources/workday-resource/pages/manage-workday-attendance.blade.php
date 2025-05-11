<x-filament::page>
    <x-filament::card>
        <x-slot name="heading">
            Gestión de Asistencia - Jornada #{{ $record->id }}
        </x-slot>

        {{ $this->form }}
    </x-filament::card>
</x-filament::page>