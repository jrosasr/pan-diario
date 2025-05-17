<?php

namespace App\Filament\Resources\DisabilityResource\Pages;

use App\Filament\Resources\DisabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDisability extends CreateRecord
{
    protected static string $resource = DisabilityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
