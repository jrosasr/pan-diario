<?php

namespace App\Filament\Reception\Resources;

use App\Filament\Reception\Resources\WorkdayResource\Pages;
use App\Filament\Reception\Resources\WorkdayResource\RelationManagers;
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
    protected static ?string $modelLabel = 'Jornada';
    protected static ?string $pluralModelLabel = 'Jornadas';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Jornadas';
    protected static ?int $navigationSort = 1;
    
    protected static ?string $tenantRelationshipName = 'workdays';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        ];
    }
}
