<?php

namespace App\Filament\Resources\WorkdayResource\Pages;

use App\Filament\Resources\WorkdayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkdays extends ListRecords
{
    protected static string $resource = WorkdayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
