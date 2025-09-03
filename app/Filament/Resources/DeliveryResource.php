<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryResource\Pages;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use App\Models\Delivery;
use App\Models\Resource as ResourceModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Mayordomía';
    protected static ?string $modelLabel = 'Entregas';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Select::make('church_id')
                ->relationship('church', 'name')
                ->searchable()
                ->preload()
                ->disabled(fn ($record) => $record && $record->delivered)
                ->label('Iglesia beneficiaria'),

            Forms\Components\Select::make('beneficiary_id')
                ->label('Beneficiario')
                ->relationship('beneficiary', 'full_name')
                ->searchable()
                ->preload()
                ->disabled(fn ($record) => $record && $record->delivered)
                ->helperText('Puede seleccionar un beneficiario.'),
            Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->disabled(fn ($record) => $record && $record->delivered)
                    ->maxLength(200),
            // Campo para agregar recursos a la entrega usando la tabla pivote delivery_resource
            Forms\Components\Repeater::make('deliveryResources')
                ->label('Recursos de entrega')
                ->disabled(fn ($record) => $record && $record->delivered)
                ->relationship('deliveryResources')
                ->schema([
                    Forms\Components\Select::make('resource_id')
                        ->options(ResourceModel::all()->pluck('name', 'id'))
                        ->required()
                        ->label('Recurso')
                        ->searchable(),
                    Forms\Components\TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->minValue(1)
                        ->label('Cantidad'),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->createItemButtonLabel('Agregar recurso')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('church.name')->label('Iglesia')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')->label('Notas')->limit(50)->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record && $record->delivered),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-document')
                    ->url(fn ($record) => route('delivery.pdf', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_pdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('delivery.pdf.view', $record->id))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
