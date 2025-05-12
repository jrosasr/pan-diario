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

class WorkdaysSummaryTable extends BaseWidget
{
    protected static ?string $heading = 'Resumen de Jornadas Finalizadas';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Workday::query()
                    ->withCount([
                        'beneficiaries as male_count' => fn ($query) => $query->where('diner', 'male'),
                        'beneficiaries as female_count' => fn ($query) => $query->where('diner', 'female'),
                        'beneficiaries as boy_count' => fn ($query) => $query->where('diner', 'boy'),
                        'beneficiaries as girl_count' => fn ($query) => $query->where('diner', 'girl'),
                        'beneficiaries as total_count'
                    ])
                    ->where('status', 'finished')
                    ->whereBetween('started_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Fecha de Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('ended_at')
                    ->label('Fecha de Finalización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('male_count')
                    ->label('Hombres')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('female_count')
                    ->label('Mujeres')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('boy_count')
                    ->label('Niños')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('girl_count')
                    ->label('Niñas')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total')
                    ->numeric()
                    ->color('primary')
                    ->weight('bold'),
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