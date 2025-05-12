<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Workday;
use App\Models\Beneficiary;
use Carbon\Carbon;

class BeneficiariesByWorkdayChart extends ChartWidget
{
    protected static ?string $heading = 'Beneficiarios por Jornada';

    protected static string $color = 'info';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $workdays = Workday::with('beneficiaries')
            ->whereBetween('started_at', [
                now()->startOfYear(),
                now()->endOfYear()
            ])
            ->get();

        $data = $workdays->map(function ($workday) {
            $startDate = is_string($workday->started_at) 
                ? Carbon::parse($workday->started_at) 
                : $workday->started_at;

            $beneficiaries = $workday->beneficiaries;
            
            return [
                'date' => $startDate->format('Y-m'),
                'total' => $beneficiaries->count(),
                'male' => $beneficiaries->where('diner', 'male')->count(),
                'female' => $beneficiaries->where('diner', 'female')->count(),
                'boy' => $beneficiaries->where('diner', 'boy')->count(),
                'girl' => $beneficiaries->where('diner', 'girl')->count(),
            ];
        });

        // Agrupar por mes
        $groupedData = $data->groupBy('date')->map(function ($monthData) {
            return [
                'date' => $monthData->first()['date'],
                'total' => $monthData->sum('total'),
                'male' => $monthData->sum('male'),
                'female' => $monthData->sum('female'),
                'boy' => $monthData->sum('boy'),
                'girl' => $monthData->sum('girl'),
            ];
        })->sortBy('date')->values();

        return [
            'datasets' => [
                [
                    'label' => 'Total Beneficiarios',
                    'data' => $groupedData->pluck('total')->toArray(),
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $groupedData->pluck('date')->toArray(),
            'detailedData' => $groupedData->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
