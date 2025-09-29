<?php

namespace App\Filament\Resources\PrayerRequestResource\Pages;

use App\Filament\Resources\PrayerRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrayerRequests extends ListRecords
{
    protected static string $resource = PrayerRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
