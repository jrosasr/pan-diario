<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessionalResource\Pages;
use App\Filament\Resources\ProfessionalResource\RelationManagers;
use App\Models\Professional;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfessionalResource extends Resource
{
    protected static ?string $model = Professional::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Recursos Humanos';
    protected static ?string $modelLabel = 'Profesional';
    protected static ?string $pluralModelLabel = 'Profesionales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Apellido')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('identification_number')
                    ->label('Cédula')
                    ->maxLength(50),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Teléfono')
                    ->maxLength(50),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->maxLength(255),
                Forms\Components\Select::make('professions')
                    ->label('Profesiones')
                    ->multiple()
                    ->relationship('professions', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('specialties')
                    ->label('Especializaciones')
                    ->multiple()
                    ->options(function (callable $get) {
                        $professionIds = $get('professions') ?? [];
                        if (empty($professionIds)) {
                            return [];
                        }
                        $specializations = collect();
                        foreach ($professionIds as $professionId) {
                            $profession = \App\Models\Profession::with('specializations')->find($professionId);
                            if ($profession) {
                                $specializations = $specializations->merge($profession->specializations);
                            }
                        }
                        return $specializations->unique('id')->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('first_name')->label('Nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('last_name')->label('Apellido')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('identification_number')->label('Cédula')->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->label('Teléfono'),
                Tables\Columns\TextColumn::make('address')->label('Dirección')->limit(30),
                Tables\Columns\TagsColumn::make('specialties.name')->label('Especialidades'),
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
            'index' => Pages\ListProfessionals::route('/'),
            'create' => Pages\CreateProfessional::route('/create'),
            'edit' => Pages\EditProfessional::route('/{record}/edit'),
        ];
    }
}
