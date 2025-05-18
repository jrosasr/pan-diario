<?php

namespace App\Filament\Resources\WorkdayResource\Pages;

use App\Filament\Resources\WorkdayResource;
use App\Models\Beneficiary;
use App\Models\Configuration;
use App\Models\Workday;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

use Filament\Actions\Action; // Import the Action class
use Filament\Actions\Concerns\InteractsWithActions; // Import the trait
use Filament\Actions\Contracts\HasActions; // Import the interface
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Exceptions\Halt;

use Illuminate\Support\Facades\Auth;

class ManageWorkdayAttendance extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $resource = WorkdayResource::class;
    protected static string $view = 'filament.resources.workday-resource.pages.manage-workday-attendance';
    // protected static string $view = 'filament.workday.attendance';

    public ?array $data = [];
    public Workday $record;
    
    public $confirmingBeneficiaryId = null;
    public $beneficiaryDetails = null;

    public function mount(Workday $record): void
    {
        // $this->record = $record;
        $this->record = $record->load('beneficiaries');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('confirmAttendance')
                ->label('Confirmar')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Asistencia')
                ->modalDescription('¿Registrar la asistencia de este beneficiario?')
                ->modalSubmitActionLabel('Aceptar')
                ->modalCancelActionLabel('Cancelar')
                ->action(function () {
                    if ($this->confirmingBeneficiaryId) {
                        $this->registerAttendance($this->confirmingBeneficiaryId);
                    } else {
                        Notification::make()
                            ->title('Error')
                            ->body('No se ha seleccionado un beneficiario.')
                            ->danger()
                            ->send();
                        throw new Halt(); // Stop further execution
                    }
                    $this->confirmingBeneficiaryId = null;
                    $this->beneficiaryDetails = null;
                    $this->dispatch('notify'); // Notify frontend to restart scan
                }),
        ];
    }

    public function confirmBeneficiary($beneficiaryId)
    {
        $this->confirmingBeneficiaryId = $beneficiaryId;
        $beneficiary = Beneficiary::find($beneficiaryId);
        if ($beneficiary) {
            $this->beneficiaryDetails = [
                'id' => $beneficiary->id,
                'full_name' => $beneficiary->full_name,
                'dni' => $beneficiary->dni,
            ];
            $this->dispatch('open-modal', id: 'confirmAttendance');
        } else {
            Notification::make()
                ->title('Beneficiario no encontrado')
                ->danger()
                ->send();
        }
    }

    public function cancelAttendance()
    {
        $this->confirmingBeneficiaryId = null;
        $this->beneficiaryDetails = null;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Escaneo de QR')
                    ->description('Escanea el código QR de un beneficiario para registrar su asistencia')
                    ->schema([
                        View::make('filament.qr-scanner')
                            ->viewData([
                                'workdayId' => $this->record->id,
                            ]),
                    ]),
                
                Section::make('Beneficiarios registrados')
                    ->description('Lista de beneficiarios que han asistido a esta jornada')
                    ->schema([
                        View::make('filament.attendance-list')
                            ->viewData([
                                'beneficiaries' => $this->record->beneficiaries ?? collect(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function registerAttendance($beneficiaryId)
    {
        try {
            $beneficiary = Beneficiary::findOrFail($beneficiaryId);
            
            // Verificar si ya está registrado
            if ($this->record->beneficiaries()->where('beneficiary_id', $beneficiaryId)->exists()) {
                Notification::make()
                    ->title('Beneficiario ya registrado')
                    ->body("{$beneficiary->full_name} ya está registrado en esta jornada")
                    ->warning()
                    ->send();
                return;
            }
            
            // Registrar asistencia
            $this->record->beneficiaries()->attach($beneficiaryId);
            
            Notification::make()
                ->title('Asistencia registrada')
                ->body("{$beneficiary->full_name} ha sido registrado en la jornada")
                ->success()
                ->send();

            $this->confirmingBeneficiaryId = null;
            $this->beneficiaryDetails = null;
                
        } catch (\Exception $e) {
            Log::error("Error registering attendance: " . $e->getMessage());
            Notification::make()
                ->title('Error')
                ->body('No se pudo registrar la asistencia')
                ->danger()
                ->send();
        }
    }

    public function showBeneficiaryConfirmation($beneficiaryId)
    {
        $beneficiary = Beneficiary::find($beneficiaryId);
        if ($beneficiary) {
            $this->confirmingBeneficiaryId = $beneficiary->id;
            $this->beneficiaryDetails = [
                'id' => $beneficiary->id,
                'full_name' => $beneficiary->full_name,
                'dni' => $beneficiary->dni,
            ];
        } else {
            Notification::make()
                ->title('Beneficiario no encontrado')
                ->danger()
                ->send();
            // Optionally, you might want to reset the scanner here (if possible from the backend)
        }
    }

    public function confirmAttendance($beneficiaryId)
    {
        try {
            $beneficiary = Beneficiary::findOrFail($beneficiaryId);

            // Obtener la configuración del equipo
            $config = Auth::user()->currentTeam()->configuration;

            // Verifica si ya existe la relación
            $alreadyRegistered = $this->record->beneficiaries()
                ->where('beneficiary_id', $beneficiaryId)
                ->exists();

            // Verificar si ya está registrado
            if ($alreadyRegistered && !$config->multiple_beneficiaries_for_workday) {
                Notification::make()
                    ->title('Beneficiario ya registrado')
                    ->body("{$beneficiary->full_name} ya está registrado en esta jornada")
                    ->warning()
                    ->send();
                return;
            }

            // Registrar asistencia
            $this->record->beneficiaries()->attach($beneficiaryId);

            Notification::make()
                ->title('Asistencia registrada')
                ->body("{$beneficiary->full_name} ha sido registrado en la jornada")
                ->success()
                ->send();

            $this->confirmingBeneficiaryId = null;
            $this->beneficiaryDetails = null;

            // Despachar evento para cerrar modal y reiniciar scanner
            $this->dispatch('close-modal'); 
            $this->dispatch('notify'); // Para reiniciar el scanner
            $this->dispatch('reset-scanner');

        } catch (\Exception $e) {
            Log::error("Error registering attendance: " . $e->getMessage());
            Notification::make()
                ->title('Error')
                ->body('No se pudo registrar la asistencia')
                ->danger()
                ->send();
        }
    }

    public function removeAttendance($beneficiaryId)
    {
        try {
            $beneficiary = Beneficiary::findOrFail($beneficiaryId);
            
            // Verificar si existe la relación antes de intentar eliminarla
            if (!$this->record->beneficiaries()->where('beneficiary_id', $beneficiaryId)->exists()) {
                Notification::make()
                    ->title('Error')
                    ->body('El beneficiario no está registrado en esta jornada')
                    ->danger()
                    ->send();
                return;
            }
            
            // Eliminar la relación
            $this->record->beneficiaries()->detach($beneficiaryId);
            
            Notification::make()
                ->title('Asistencia eliminada')
                ->body("Se ha removido a {$beneficiary->full_name} de la jornada")
                ->success()
                ->send();
                
            // Actualizar la lista de beneficiarios
            $this->record->refresh();
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('No se pudo eliminar la asistencia: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getBeneficiaryInfo($beneficiaryId)
    {
        $beneficiary = Beneficiary::findOrFail($beneficiaryId);
        
        return [
            'active' => $beneficiary->active, // Asumiendo que el campo se llama 'active' en tu modelo
            'photo' => $beneficiary->photo, // Ruta relativa de la foto
        ];
    }
}