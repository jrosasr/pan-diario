<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicationResource\Pages;
use App\Filament\Resources\MedicationResource\RelationManagers;
use App\Models\Medication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class MedicationResource extends Resource
{
    protected static ?string $model = Medication::class;
    protected static ?string $modelLabel = 'Medicamentos';
    protected static ?string $pluralModelLabel = 'Medicamentos';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Config General';
    protected static ?int $navigationSort = 3;

    protected static ?string $tenantRelationshipName = 'medications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label('Nombre del medicamento')
                    ->required(),
                TextInput::make('notes')
                    ->label('Notas'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->sortable()->searchable(),
                TextColumn::make('notes')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
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
            'index' => Pages\ListMedications::route('/'),
            'create' => Pages\CreateMedication::route('/create'),
            'edit' => Pages\EditMedication::route('/{record}/edit'),
        ];
    }
}
