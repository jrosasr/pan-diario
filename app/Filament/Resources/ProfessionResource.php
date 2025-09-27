<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessionResource\Pages;
use App\Filament\Resources\ProfessionResource\RelationManagers;
use App\Models\Profession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfessionResource extends Resource
{
    protected static ?string $model = Profession::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Voluntariado';
    protected static ?string $modelLabel = 'Profesión';
    protected static ?string $pluralModelLabel = 'Profesiones';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->label('Profesión'),
                Forms\Components\Select::make('specializations')
                    ->label('Especializaciones')
                    ->multiple()
                    ->relationship('specializations', 'name')
                    ->preload()
                    ->searchable()
                    ->helperText('Puede seleccionar una o varias especializaciones para esta profesión.')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nueva especialización')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Hidden::make('team_id')
                            ->default(fn () => auth()->user()->currentTeam()->id),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Profesión')->sortable()->searchable(),
                Tables\Columns\TagsColumn::make('specializations.name')
                    ->label('Especializaciones')
                    ->separator(',')
                    ->tooltip(fn ($record) => $record->specializations->pluck('name')->join(', ')),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Creado')->sortable(),
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
            'index' => Pages\ListProfessions::route('/'),
            'create' => Pages\CreateProfession::route('/create'),
            'edit' => Pages\EditProfession::route('/{record}/edit'),
        ];
    }
}
