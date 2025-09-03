<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DeliveryResource;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Illuminate\Support\Facades\Storage;

class EditDelivery extends EditRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        if ($this->record->delivered) {
            return [];
        }

        return [
            Action::make('Confirmar Entrega')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->modalHeading('Firmas de entrega')
                ->modalSubmitActionLabel('Confirmar y guardar firmas')
                ->form([
                    SignaturePad::make('signature_beneficiary')
                        ->label('Firma del beneficiario')
                        ->dotSize(2.0)
                        ->lineMinWidth(1)
                        ->lineMaxWidth(3)
                        ->penColor('#000')
                        ->backgroundColor('rgba(0,0,0,0)')
                        ->confirmable()
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('deliverer_name')
                        ->label('Nombre de quien entrega')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('deliverer_dni')
                        ->label('Cédula de quien entrega')
                        ->required(),
                    SignaturePad::make('signature_deliverer')
                        ->label('Firma de quien entrega')
                        ->dotSize(2.0)
                        ->lineMinWidth(1)
                        ->lineMaxWidth(3)
                        ->penColor('#000')
                        ->backgroundColor('rgba(0,0,0,0)')
                        ->confirmable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $beneficiaryPath = null;
                    $delivererPath = null;
                    if (!empty($data['signature_beneficiary'])) {
                        $beneficiaryPath = 'signatures/beneficiary_' . $this->record->id . '_' . time() . '.png';
                        Storage::disk('public')->put($beneficiaryPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['signature_beneficiary'])));
                    }
                    if (!empty($data['signature_deliverer'])) {
                        $delivererPath = 'signatures/deliverer_' . $this->record->id . '_' . time() . '.png';
                        Storage::disk('public')->put($delivererPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['signature_deliverer'])));
                    }
                    $this->record->update([
                        'delivered' => true,
                        'signature_beneficiary' => $beneficiaryPath,
                        'signature_deliverer' => $delivererPath,
                        'deliverer_name' => $data['deliverer_name'],
                        'deliverer_dni' => $data['deliverer_dni'],
                    ]);

                    Notification::make()
                        ->title('¡Entrega confirmada con éxito!')
                        ->success()
                        ->send();

                    return $this->getResource()::getUrl('edit', ['record' => $this->record->getKey()]);
                }),
        ];
    }

}
