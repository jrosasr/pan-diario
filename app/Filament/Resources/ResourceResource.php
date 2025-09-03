<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Filament\Resources\ResourceResource\RelationManagers;
use App\Models\Resource as ResourceModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Mayordomía';
    protected static ?string $modelLabel = 'Insumos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipo de insumo')
                    ->options([
                        'food' => 'Comida',
                        'medicine' => 'Medicina',
                    ])
                    ->required()
                    ->helperText('Ejemplo: comida, medicina, etc.'),
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->helperText('Expresado en unidades')
                    ->numeric(),
                // Forms\Components\Select::make('unit_of_measure')
                //     ->label('Unidad de medida')
                //     ->options([
                //         'kg' => 'Kilogramos',
                //         'gr' => 'Gramos',
                //         'l' => 'Litros',
                //         'ml' => 'Mililitros',
                //         'units' => 'Unidades',
                //     ])
                //     ->helperText('Ejemplo: kg, litros, unidades, etc.'),
                Forms\Components\DatePicker::make('expiration_date')
                    ->label('Fecha de expiración')
                    ->nullable()
                    ->native(false),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(200),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('type')
                        ->label('Tipo')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'food' => 'Comida',
                            'medicine' => 'Medicamentos',
                            default => $state,
                        })
                        ->icon(fn ($record) => match ($record->type) {
                            'food' => 'heroicon-o-inbox-stack',
                            'medicine' => 'heroicon-o-beaker',
                            default => null,
                        })
                        ->color(fn ($record) => match ($record->type) {
                            'food' => 'success',
                            'medicine' => 'warning',
                            default => 'secondary',
                        })
                        ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Fecha de expiración')
                    ->sortable(),
            ])
            ->filters([
                // filter by type
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'food' => 'Comida',
                        'medicine' => 'Medicamentos',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'edit' => Pages\EditResource::route('/{record}/edit'),
        ];
    }
}
