@php
    $beneficiaries = $beneficiaries ?? collect();
    $readOnly = $readOnly ?? false;
@endphp

<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center gap-2">
            <x-heroicon-o-user-group class="h-5 w-5" />
            <span>{{ $readOnly ? 'Beneficiarios que asistieron' : 'Registro de Asistencia' }}</span>
        </div>
    </x-slot>

    @if ($beneficiaries->count() > 0)
        <x-filament-tables::container class="mt-4">
            <x-filament-tables::table>
                <x-slot name="header">
                    <x-filament-tables::header-cell>
                        Nombre
                    </x-filament-tables::header-cell>
                    @if (!$readOnly)
                        <x-filament-tables::header-cell width="10%">
                            Acciones
                        </x-filament-tables::header-cell>
                    @endif
                </x-slot>

                @foreach ($beneficiaries as $beneficiary)
                    <x-filament-tables::row>
                        <x-filament-tables::cell class="text-sm">
                            {{ $beneficiary->full_name }}({{ $beneficiary->dni }})
                        </x-filament-tables::cell>

                        @if (!$readOnly)
                            <x-filament-tables::cell class="text-right">
                                <x-filament::button wire:click="removeAttendance({{ $beneficiary->id }})" size="sm"
                                    color="danger" icon="heroicon-o-trash" tooltip="Eliminar asistencia" />
                            </x-filament-tables::cell>
                        @endif
                    </x-filament-tables::row>
                @endforeach
            </x-filament-tables::table>
        </x-filament-tables::container>
    @else
        <x-filament::card>
            <div class="text-center">
                <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $readOnly ? 'Ningún beneficiario asistió' : 'No hay beneficiarios registrados aún' }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $readOnly ? 'No se registraron asistencias para esta jornada' : 'Escanea códigos QR para registrar asistencias' }}
                </p>
            </div>
        </x-filament::card>
    @endif
</x-filament::section>
