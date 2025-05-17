<?php

namespace App\Filament\Widgets;

use App\Models\Workday;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;

class WorkdaysSummaryTable extends BaseWidget
{
    protected static ?string $heading = 'Resumen de Jornadas Finalizadas';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        // Obtener el equipo (tenant) actual
        $teamId = Auth::user()->currentTeam()->id;

        return $table
            ->query(
                Workday::query()
                    ->where('team_id', $teamId)
                    ->withCount([
                        'beneficiaries as male_count' => fn ($query) => $query->where('diner', 'male'),
                        'beneficiaries as female_count' => fn ($query) => $query->where('diner', 'female'),
                        'beneficiaries as boy_count' => fn ($query) => $query->where('diner', 'boy'),
                        'beneficiaries as girl_count' => fn ($query) => $query->where('diner', 'girl'),
                        'beneficiaries as total_count'
                    ])
                    // ->where('status', 'finished')
                    ->whereBetween('started_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth(),
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Fecha')
                    ->date(),
                Tables\Columns\TextColumn::make('male_count')
                    ->label('Hombres')
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Hombres')), // Suma de hombres
                Tables\Columns\TextColumn::make('female_count')
                    ->label('Mujeres')
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Mujeres')), // Suma de mujeres
                Tables\Columns\TextColumn::make('boy_count')
                    ->label('Niños')
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Niños')),   // Suma de niños
                Tables\Columns\TextColumn::make('girl_count')
                    ->label('Niñas')
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Niñas')),   // Suma de niñas
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total')
                    ->numeric()
                    ->color('primary')
                    ->weight('bold')
                    ->summarize(Sum::make()->label('Total General')), // Suma total
            ])
            ->filters([
                Filter::make('date_filter')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Fecha desde')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('end_date')
                            ->label('Fecha hasta')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('started_at', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('started_at', '<=', $date),
                            );
                    })
            ])
            ->defaultSort('started_at', 'desc');
    }
}
