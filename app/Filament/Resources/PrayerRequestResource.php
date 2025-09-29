<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrayerRequestResource\Pages;
use App\Filament\Resources\PrayerRequestResource\RelationManagers;
use App\Models\PrayerRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrayerRequestResource extends Resource
{
    protected static ?string $model = PrayerRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Acompañamiento y Bienestar';
    protected static ?string $modelLabel = 'Peticiones y consejerias';
    protected static ?string $pluralModelLabel = 'Peticiones y consejerias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(30),
                Forms\Components\Select::make('request_type')
                    ->label('Clasificación de la petición')
                    ->options([
                        'marriage' => 'Matrimonio',
                        'family' => 'Familia',
                        'parenthood' => 'Paternidad',
                        'premarital_counseling' => 'Consejería Prematrimonial',
                        'anxiety_stress_depression' => 'Ansiedad, Estrés, Depresión',
                        'anger' => 'Ira',
                        'divorce' => 'Divorcio',
                        'grief' => 'Duelo',
                        'struggle_with_sin' => 'Lucha con el Pecado',
                        'self_esteem' => 'Autoestima',
                        'doubts' => 'Dudas',
                        'confusion' => 'Confusión',
                        'other' => 'Otro',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3)
                    ->required(),
                Forms\Components\Select::make('petition_type')
                    ->label('Tipo de Petición')
                    ->options([
                        'prayer' => 'Oración',
                        'counseling' => 'Consejería',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estatus')
                    ->options([
                        // 'attended' => 'Asistido',
                        'pending' => 'Pendiente',
                        'cancelled' => 'Cancelado',
                        'reassigned' => 'Reasignado',
                        'rescheduled' => 'Reprogramado',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Petición realizada'),
                Forms\Components\DateTimePicker::make('appointment_date')
                    ->label('Fecha de reunión'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
                Tables\Columns\BadgeColumn::make('request_type')->label('Clasificación de la petición'),
                Tables\Columns\BadgeColumn::make('petition_type')->label('Tipo de Petición'),
                Tables\Columns\TextColumn::make('date')->date('Y-m-d')->label('Fecha de la petición'),
                Tables\Columns\BadgeColumn::make('status')->label('Estatus'),
                Tables\Columns\TextColumn::make('appointment_date')->dateTime('Y-m-d H:i')->label('Fecha de reunión'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        // 'attended' => 'Asistido',
                        'pending' => 'Pendiente',
                        'cancelled' => 'Cancelado',
                        'reassigned' => 'Reasignado',
                        'rescheduled' => 'Reprogramado',
                    ]),
                Tables\Filters\SelectFilter::make('petition_type')
                    ->label('Tipo de Petición')
                    ->options([
                        'prayer' => 'Oración',
                        'counseling' => 'Consejería',
                    ]),
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
            'index' => Pages\ListPrayerRequests::route('/'),
            'create' => Pages\CreatePrayerRequest::route('/create'),
            'edit' => Pages\EditPrayerRequest::route('/{record}/edit'),
        ];
    }
}
