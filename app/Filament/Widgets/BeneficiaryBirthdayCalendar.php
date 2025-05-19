<?php

namespace App\Filament\Widgets;

use App\Models\Beneficiary;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;
use Illuminate\Support\Facades\Auth;

class BeneficiaryBirthdayCalendar extends FullCalendarWidget
{
    protected static ?string $heading = 'Calendario de Cumpleaños';
    
    protected static ?int $sort = 3;

    public function fetchEvents(array $fetchInfo): array
    {
        return Beneficiary::query()
            ->where('team_id', Auth::user()->currentTeam()->id)
            ->whereNotNull('birthdate')
            ->get()
            ->map(function (Beneficiary $beneficiary) {
                $birthdate = \Carbon\Carbon::parse($beneficiary->birthdate);
                $currentYear = now()->year;
                $nextBirthday = $birthdate->copy()->year($currentYear);
                
                if ($nextBirthday->isPast()) {
                    $nextBirthday->addYear();
                }

                $age = $nextBirthday->diffInYears($birthdate);
                
                return EventData::make()
                    ->id($beneficiary->id)
                    ->title($beneficiary->full_name . ' - ' . $age . ' años')
                    ->start($nextBirthday->toDateString())
                    ->allDay(true)
                    ->backgroundColor('#3b82f6')
                    ->borderColor('#3b82f6')
                    ->extendedProps([
                        'photo' => $beneficiary->photo ? asset('storage/' . $beneficiary->photo) : null,
                        'dni' => $beneficiary->dni,
                        'phone' => $beneficiary->phone,
                        'address' => $beneficiary->address,
                    ]);
            })
            ->toArray();
    }

    public function config(): array
    {
        return [
            'initialView' => 'dayGridMonth',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth',
            ],
            'eventDisplay' => 'block',
            'eventClick' => "function(info) { info.jsEvent.preventDefault(); }",
        ];
    }

    public function eventDidMount(): string
    {
        return <<<'JS'
        function(info) {
            const event = info.event;
            const el = info.el;
            
            // Mostrar foto si existe
            if (event.extendedProps.photo) {
                const img = document.createElement('img');
                img.src = event.extendedProps.photo;
                img.style.width = '40px';
                img.style.height = '40px';
                img.style.borderRadius = '50%';
                img.style.marginRight = '8px';
                img.style.objectFit = 'cover';
                
                const title = el.querySelector('.fc-event-title');
                if (title) {
                    title.parentNode.insertBefore(img, title);
                }
            }
            
            // Tooltip con más información
            el.setAttribute('x-data', '{ tooltip: `' + 
                `<div class="p-2">
                    <div class="flex items-center gap-2 mb-2">
                        ${event.extendedProps.photo ? `<img src="${event.extendedProps.photo}" class="w-10 h-10 rounded-full object-cover">` : ''}
                        <strong>${event.title}</strong>
                    </div>
                    <div class="text-sm space-y-1">
                        <p><span class="font-medium">DNI:</span> ${event.extendedProps.dni || 'N/A'}</p>
                        <p><span class="font-medium">Teléfono:</span> ${event.extendedProps.phone || 'N/A'}</p>
                        <p><span class="font-medium">Dirección:</span> ${event.extendedProps.address || 'N/A'}</p>
                    </div>
                </div>` + 
                '` }');
            el.setAttribute('x-tooltip.html', 'tooltip');
        }
        JS;
    }
}