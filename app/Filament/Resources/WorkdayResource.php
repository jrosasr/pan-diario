<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkdayResource\Pages;
use App\Filament\Resources\WorkdayResource\RelationManagers;
use App\Models\Workday;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\View;

use Filament\Tables\Columns\TextColumn;


class WorkdayResource extends Resource
{
    protected static ?string $model = Workday::class;
    protected static ?string $tenantRelationshipName = 'workdays';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $modelLabel = 'Jornada';
    protected static ?string $pluralModelLabel = 'Jornadas';

    public static function form(Form $form): Form
    {
        $record = $form->getRecord();

        return $form
        ->schema([
            DateTimePicker::make('started_at')
                ->label('Fecha de inicio')
                ->required()
                ->disabled(fn (string $operation): bool => $operation === 'edit' && $record->status === 'finished'),
                
            DateTimePicker::make('ended_at')
                ->label('Fecha de finalización')
                ->visibleOn('edit'),
                
            Select::make('status')
            ->default('in-process')
            ->required()
            ->label('Estatus de la jornada')
            ->options([
                'in-process' => 'En curso',
                'finished' => 'Finalizado',
                ])
                ->disabled(fn (string $operation): bool => $operation === 'edit' && $record->status === 'finished'),
                // Mostrar tabla solo cuando la jornada está finalizada
                View::make('filament.attendance-list')
                ->visibleOn('edit')
                ->viewData([
                    'beneficiaries' => $record->beneficiaries ?? collect(),
                    'readOnly' => $record?->status === 'finished',
                ]),

        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('started_at')->label('Fecha de inicio')->sortable()->searchable(),
                TextColumn::make('ended_at')->label('Fecha de finalización')->default('N/A')->sortable()->searchable(),
                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in-process' => 'En curso',
                        'finished' => 'Finalizado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'in-process' => 'warning',
                        'finished' => 'success',
                    })
            ])
            ->filters([
                //
            ])
            ->defaultSort('id', 'desc') // Agrega esta línea
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\Action::make('manageAttendance')
                    ->label('Gestionar Asistencia')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->visible(fn (Workday $record): bool => $record->status === 'in-process')
                    ->url(fn (Workday $record): string => static::getUrl('attendance', ['record' => $record])),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkdays::route('/'),
            'create' => Pages\CreateWorkday::route('/create'),
            'edit' => Pages\EditWorkday::route('/{record}/edit'),
            'attendance' => Pages\ManageWorkdayAttendance::route('/{record}/attendance'),
        ];
    }
}
