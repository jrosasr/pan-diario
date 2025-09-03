<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDelivery extends CreateRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function afterCreate(): void
    {
        $this->record->refresh();
        foreach ($this->record->deliveryResources as $deliveryResource) {
            $resource = $deliveryResource->resource;
            if ($resource && $deliveryResource->quantity) {
                $resource->decrement('quantity', $deliveryResource->quantity);
            }
        }
    }
}
