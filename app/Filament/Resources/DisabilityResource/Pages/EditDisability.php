<?php

namespace App\Filament\Resources\DisabilityResource\Pages;

use App\Filament\Resources\DisabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDisability extends EditRecord
{
    protected static string $resource = DisabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
