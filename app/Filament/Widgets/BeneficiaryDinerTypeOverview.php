<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;

class BeneficiaryDinerTypeOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Obtener el equipo (tenant) actual
        $teamId = Auth::user()->currentTeam()->id;

        // Contar beneficiarios por cada tipo de comensal
        $maleCount = Beneficiary::where('team_id', $teamId)
            ->where('diner', 'male')
            ->count();

        $femaleCount = Beneficiary::where('team_id', $teamId)
            ->where('diner', 'female')
            ->count();

        $boyCount = Beneficiary::where('team_id', $teamId)
            ->where('diner', 'boy')
            ->count();

        $girlCount = Beneficiary::where('team_id', $teamId)
            ->where('diner', 'girl')
            ->count();

        return [
            Stat::make('Hombres', $maleCount)
                ->description('adultos masculinos')
                ->color('primary')
                ->icon('heroicon-o-user'),

            Stat::make('Mujeres', $femaleCount)
                ->description('adultos femeninos')
                ->color('pink')
                ->icon('heroicon-o-user'),

            Stat::make('Niños', $boyCount)
                ->description('niños')
                ->color('blue')
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Niñas', $girlCount)
                ->description('niñas')
                ->color('purple')
                ->icon('heroicon-o-academic-cap'),
        ];
    }
}
