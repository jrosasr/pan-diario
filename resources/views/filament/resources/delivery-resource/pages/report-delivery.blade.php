@extends('filament::page')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-bold">Reporte de Entrega</h1>
        <form wire:submit.prevent="submit">
            {{ $this->form }}
            <button type="submit" class="filament-button filament-button-primary mt-4">
                Guardar reporte
            </button>
        </form>
    </div>
@endsection
