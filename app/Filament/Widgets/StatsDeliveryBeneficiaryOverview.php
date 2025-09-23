<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Facades\Filament;

class StatsDeliveryBeneficiaryOverview extends BaseWidget
{

    protected ?string $heading = 'Beneficiarios atendidos en las entregas';

    protected ?string $description = 'Un resumen de algunas analíticas clave sobre los beneficiarios atendidos en las entregas.';

    protected function getStats(): array
    {
        // Obtiene el ID del equipo (tenant) actual utilizando Filament.
        $teamId = Filament::getTenant()->id;

        $data = $this->getStatsPerDelivery();

        return [
            Stat::make('Hombres', Delivery::where('team_id', $teamId)->sum('men_count'))
                ->description('Total de hombres beneficiados')
                ->icon('heroicon-o-user')
                ->chart($data['men_count']),
            Stat::make('Mujeres', Delivery::where('team_id', $teamId)->sum('women_count'))
                ->description('Total de mujeres beneficiadas')
                ->icon('heroicon-o-user')
                ->chart($data['women_count']),
            Stat::make('Niños', Delivery::where('team_id', $teamId)->sum('boys_count'))
                ->description('Total de niños beneficiados')
                ->icon('heroicon-o-academic-cap')
                ->chart($data['boys_count']),
            Stat::make('Niñas', Delivery::where('team_id', $teamId)->sum('girls_count'))
                ->description('Total de niñas beneficiadas')
                ->icon('heroicon-o-academic-cap')
                ->chart($data['girls_count']),
            Stat::make('Total', array_sum($data['total_count']))
                ->description('Total de beneficiarios atendidos')
                ->icon('heroicon-o-users')
                ->chart($data['total_count'])
                ->color('primary'),
        ];
    }

    /**
     * Obtiene el total de beneficiarios por tipo en un rango de fechas.
     *
     * @param string|null $from Fecha inicio (Y-m-d) opcional
     * @param string|null $to   Fecha fin (Y-m-d) opcional
     * @return array
     */
    protected function getStatsPerDelivery(?string $from = null, ?string $to = null): array
    {
        // Obtiene el ID del equipo (tenant) actual y lo agrega a la consulta.
        $teamId = Filament::getTenant()->id;
        $query = Delivery::query()->where('team_id', $teamId);

        if ($from) {
            $query->whereDate('delivered_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('delivered_at', '<=', $to);
        }
        // Agrupar por fecha (día)
        $grouped = $query->selectRaw('
                DATE(delivered_at) as date,
                SUM(men_count) as men_count,
                SUM(women_count) as women_count,
                SUM(boys_count) as boys_count,
                SUM(girls_count) as girls_count
            ')
            ->groupByRaw('DATE(delivered_at)')
            ->orderBy('date')
            ->get();

        // Agrupa por fecha y suma los conteos retornando un valor de array
        $groupedTotal = $grouped->map(fn($item) =>
            (int)$item->men_count + (int)$item->women_count + (int)$item->boys_count + (int)$item->girls_count,
        )->toArray();

        return [
            'men_count' => $grouped->pluck('men_count')->map(fn($v) => (int)$v)->toArray(),
            'women_count' => $grouped->pluck('women_count')->map(fn($v) => (int)$v)->toArray(),
            'boys_count' => $grouped->pluck('boys_count')->map(fn($v) => (int)$v)->toArray(),
            'girls_count' => $grouped->pluck('girls_count')->map(fn($v) => (int)$v)->toArray(),
            'total_count' => $groupedTotal,
        ];
    }
}
