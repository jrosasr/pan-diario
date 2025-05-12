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

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\View;

use Carbon\Carbon;

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
            DatePicker::make('started_at')
                ->label('Fecha')
                ->required()
                ->native(false)
                ->disabledOn('edit'),
                
            TimePicker::make('start_time_at')
                ->label('Hora de inicio')
                ->seconds(false)
                ->required()
                ->disabledOn('edit'),
                
            TimePicker::make('end_time_at')
                ->label('Hora de finalización')
                ->seconds(false)
                ->visibleOn('edit')
                ->disabled(fn (string $operation): bool => $operation === 'edit' && $record?->status === 'finished'),
                
            Select::make('status')
                ->default('in-process')
                ->required()
                ->label('Estatus de la jornada')
                ->options([
                    'in-process' => 'En curso',
                    'finished' => 'Finalizado',
                ])
                ->disabled(fn (string $operation): bool => $operation === 'edit' && $record?->status === 'finished'),
                
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
                TextColumn::make('started_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                        
                TextColumn::make('start_time_at')
                    ->label('Hora inicio')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('h:i A') : '')
                    ->sortable(),
                    
                TextColumn::make('end_time_at')
                    ->label('Hora fin')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('h:i A') : 'N/A')
                    ->sortable(),
                    
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
            ->defaultSort('started_at', 'desc')
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
