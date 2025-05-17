<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisabilityResource\Pages;
use App\Filament\Resources\DisabilityResource\RelationManagers;
use App\Models\Disability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Support\Facades\Auth;

class DisabilityResource extends Resource
{
    protected static ?string $model = Disability::class;
    protected static ?string $modelLabel = 'Discapacidades';
    protected static ?string $pluralModelLabel = 'Discapacidades';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Config General';
    protected static ?int $navigationSort = 5;

    protected static ?string $tenantRelationshipName = 'disabilities';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label('Nombre de la discapacidad')
                    ->required(),
                TextInput::make('notes')
                    ->label('Notas'),
                Forms\Components\Hidden::make('team_id')  // Campo oculto para almacenar el team_id
                    ->default(function () {
                        return Auth::user()->currentTeam()->id;
                    }),
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
            'index' => Pages\ListDisabilities::route('/'),
            'create' => Pages\CreateDisability::route('/create'),
            'edit' => Pages\EditDisability::route('/{record}/edit'),
        ];
    }
}
