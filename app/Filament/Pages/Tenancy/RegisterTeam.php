<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Registrar Comedor';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre del comedor')
                    ->required()
                    ->maxLength(25),
                TextInput::make('address')->label('Dirección')->maxLength(60),
                FileUpload::make('logo')
                    ->required()
                    ->label('Logo')
                    ->directory('team/logos')
                    ->visibility('public')
                    ->image()
                    ->preserveFilenames()
                    ->imagePreviewHeight('150')
                    ->default(function ($record) {
                        return $record?->logo ? [asset('storage/'.$record->logo)] : null;
                    }),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create($data);

        $team->members()->attach(auth()->user());

        // Create configuration for the team
        $team->configuration()->create([
            'multiple_beneficiaries_for_workday' => false,
        ]);

        return $team;
    }
}