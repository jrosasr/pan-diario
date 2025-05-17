<?php

namespace App\Filament\Resources\WorkdayResource\Pages;

use App\Filament\Resources\WorkdayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkday extends CreateRecord
{
    protected static string $resource = WorkdayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'in-process';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
