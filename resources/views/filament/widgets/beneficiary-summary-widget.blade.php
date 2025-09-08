{{--  <div class="p-4 bg-white rounded-xl shadow border border-neutral-200 dark:bg-zinc-800 dark:border-neutral-700">
    <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
        <div>
            <label>Desde:</label>
            <input type="date" wire:model.lazy="startDate" class="border rounded px-2 py-1" />
        </div>
        <div>
            <label>Hasta:</label>
            <input type="date" wire:model.lazy="endDate" class="border rounded px-2 py-1" />
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-blue-100 text-blue-900 rounded p-3 text-center">
            <div class="text-2xl font-bold">{{ $summary['men'] }}</div>
            <div>Hombres</div>
        </div>
        <div class="bg-pink-100 text-pink-900 rounded p-3 text-center">
            <div class="text-2xl font-bold">{{ $summary['women'] }}</div>
            <div>Mujeres</div>
        </div>
        <div class="bg-yellow-100 text-yellow-900 rounded p-3 text-center">
            <div class="text-2xl font-bold">{{ $summary['boys'] }}</div>
            <div>Niños</div>
        </div>
        <div class="bg-purple-100 text-purple-900 rounded p-3 text-center">
            <div class="text-2xl font-bold">{{ $summary['girls'] }}</div>
            <div>Niñas</div>
        </div>
    </div>
    <div>
        <h4 class="font-semibold mb-2">Beneficiarios en entregas del rango:</h4>
        <ul class="list-disc pl-5">
            @forelse($summary['beneficiaries'] as $beneficiary)
                <li>{{ $beneficiary->full_name }} ({{ $beneficiary->dni }})</li>
            @empty
                <li>No hay beneficiarios en este rango.</li>
            @endforelse
        </ul>
    </div>
</div>  --}}
<div>
    <h1></h1>
</div>