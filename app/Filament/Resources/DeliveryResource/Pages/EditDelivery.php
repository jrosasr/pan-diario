<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DeliveryResource;

class EditDelivery extends EditRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        // Agregar el botón de confirmación solo si la entrega no está confirmada
        if ($this->record->delivered) {
            return [];
        }

        return [
            Action::make('Confirmar Entrega')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    // Update the 'delivered' field
                    $this->record->update(['delivered' => true]);

                    // Send a notification to the user
                    Notification::make()
                        ->title('¡Entrega confirmada con éxito!')
                        ->success()
                        ->send();

                    // Optional: redirecciona a la tabla
                    return $this->getResource()::getUrl('edit', $this->record);
                }),
        ];
    }

}
