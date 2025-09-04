<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Models\Delivery;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;

class ReportDelivery extends Page
{
    protected static string $resource = 'App\\Filament\\Resources\\DeliveryResource';
    protected static string $view = 'filament.resources.delivery-resource.pages.report-delivery';

    public Delivery $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = Delivery::findOrFail($record);
        $this->form->fill($this->record->only([
            'men_seniors_count',
            'women_seniors_count',
            'men_count',
            'women_count',
            'boys_count',
            'girls_count',
        ]));
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('men_seniors_count')->label('Hombres mayores')->numeric()->required(),
            TextInput::make('women_seniors_count')->label('Mujeres mayores')->numeric()->required(),
            TextInput::make('men_count')->label('Hombres')->numeric()->required(),
            TextInput::make('women_count')->label('Mujeres')->numeric()->required(),
            TextInput::make('boys_count')->label('Niños')->numeric()->required(),
            TextInput::make('girls_count')->label('Niñas')->numeric()->required(),
            SpatieMediaLibraryFileUpload::make('images')
                ->collection('images')
                ->multiple()
                ->maxFiles(10)
                ->label('Fotos del reporte'),
        ];
    }

    public function submit()
    {
        $this->record->update($this->form->getState());
        $this->form->saveUploadedFiles();
        Notification::make()
            ->success()
            ->title('Reporte guardado')
            ->body('La información del reporte se ha guardado correctamente.')
            ->send();
        $this->redirect(self::getResource()::getUrl('index'));
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('submit')
                ->label('Guardar reporte')
                ->action('submit'),
        ];
    }
}
