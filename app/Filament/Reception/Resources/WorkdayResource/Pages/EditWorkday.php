<?php

namespace App\Filament\Reception\Resources\WorkdayResource\Pages;

use App\Filament\Reception\Resources\WorkdayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkday extends EditRecord
{
    protected static string $resource = WorkdayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
