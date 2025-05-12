<?php

namespace App\Filament\Resources\WorkdayResource\Pages;

use App\Filament\Resources\WorkdayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class EditWorkday extends EditRecord
{
    protected static string $resource = WorkdayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($data['status'] === 'finished' && empty($data['end_time_at'])) {
            $data['end_time_at'] = Carbon::now()->format('H:i:s');
        }

        $record->update($data);

        return $record;
    }
}
