<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsDeliveryBeneficiaryOverview extends BaseWidget
{

    protected ?string $heading = 'Beneficiarios atendidos en las entregas';

    protected ?string $description = 'Un resumen de algunas analíticas clave sobre los beneficiarios atendidos en las entregas.';

    protected function getStats(): array
    {
        return [
            Stat::make('Hombres', Delivery::sum('men_count'))
                ->description('Total de hombres beneficiados')
                ->icon('heroicon-o-user'),
            Stat::make('Mujeres', Delivery::sum('women_count'))
                ->description('Total de mujeres beneficiadas')
                ->icon('heroicon-o-user'),
            Stat::make('Niños', Delivery::sum('boys_count'))
                ->description('Total de niños beneficiados')
                ->icon('heroicon-o-academic-cap'),
            Stat::make('Niñas', Delivery::sum('girls_count'))
                ->description('Total de niñas beneficiadas')
                ->icon('heroicon-o-academic-cap'),
        ];
    }
}
