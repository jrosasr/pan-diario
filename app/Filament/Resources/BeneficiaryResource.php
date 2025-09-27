<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Filament\Resources\BeneficiaryResource\RelationManagers;
use App\Models\Beneficiary;
use App\Models\Treatment;
use App\Models\Medication;
use App\Models\Disability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Toggle;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;
use App\Rules\UniqueDniForTeam;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;
    protected static ?string $modelLabel = 'Beneficiario';
    protected static ?string $pluralModelLabel = 'Beneficiarios';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $tenantRelationshipName = 'beneficiaries';
    protected static ?int $navigationSort = 4;


    protected static ?string $navigationGroup = 'Mayordomía';


    public static function form(Form $form): Form
    {
        $record = $form->getRecord();

        $teamId = Auth::user()->currentTeam()->id;

        return $form
            ->schema([
                ViewField::make('qr_code')
                ->label('Código QR')
                ->visibleOn('edit')
                ->columnSpan('full')
                ->view('filament.qr-code')
                ->viewData([
                    'qrCodeUrl' => $record ? asset('storage/' . $record->qr_code) : null,
                    'recordExists' => (bool) $record,
                ])
                ->extraAttributes(['class' => 'text-center']),

                TextInput::make('full_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre completo')
                    ->disabledOn('edit'),
                Select::make('dni_type')
                    ->options([
                        'V' => 'Nacional (V)',
                        'E' => 'Extranjero (E)',
                    ])
                    ->label('Tipo de documento de identidad'),
                TextInput::make('dni')
                    ->label('Documento de Identidad')
                    ->maxLength(20)
                    ->rules([
                        new UniqueDniForTeam($record?->id), // Pasa el ID del registro a la regla
                    ]),
                DatePicker::make('birthdate')
                    ->required()
                    ->label('Fecha de nacimiento'),
                Select::make('diner')
                    ->options([
                        'male' => 'Hombre',
                        'female' => 'Mujer',
                        'boy' => 'Niño',
                        'girl' => 'Niña',
                    ])->required()
                    ->label('Comensal')
                    ->disabledOn('edit'),
                Textarea::make('address')->maxLength(255)->label('Dirección')->columnSpan('full'),
                TextInput::make('weight')->numeric()->inputMode('decimal')->label('Peso'),
                FileUpload::make('photo')
                    ->label('Foto')
                    ->directory('beneficiaries/photos')
                    ->visibility('public')
                    ->image()
                    ->preserveFilenames()
                    ->imagePreviewHeight('150')
                    ->openable()
                    ->downloadable()
                    ->default(function ($record) {
                        return $record?->photo ? [asset('storage/'.$record->photo)] : null;
                    }),

                FileUpload::make('dni_photo')
                    ->label('Foto DNI')
                    ->directory('beneficiaries/dni')
                    ->visibility('public')
                    ->image()
                    ->preserveFilenames(),
                
                TextInput::make('phone')->tel()->label('Teléfono de emergencia'),
                TextInput::make('alt_phone')->tel()->label('Teléfono alternativo'),
                Select::make('disability_ids')
                    ->label('Discapacidades')
                    ->multiple()
                    ->preload()
                    ->relationship('disabilities', 'description')
                    ->options(Disability::all()->pluck('description', 'id'))
                    ->createOptionForm([
                        TextInput::make('description')
                            ->label('Nombre de la discapacidad')
                            ->required(),
                        TextInput::make('notes')
                            ->label('Notas'),
                        Forms\Components\Hidden::make('team_id')  // Campo oculto para almacenar el team_id
                            ->default(fn () => Filament::getTenant()->id),
                    ]),
                Select::make('treatment_ids')
                    ->label('Tratamientos')
                    ->multiple()
                    ->preload()
                    ->relationship('treatments', 'description')
                    ->options(Treatment::all()->pluck('description', 'id'))
                    ->createOptionForm([
                        TextInput::make('description')
                            ->label('Nombre del Tratamiento')
                            ->required(),
                        TextInput::make('notes')
                            ->label('Notas'),
                        Forms\Components\Hidden::make('team_id')  // Campo oculto para almacenar el team_id
                            ->default(fn () => Filament::getTenant()->id),
                    ]),
                Select::make('medication_ids')
                    ->label('Medicamentos')
                    ->multiple()
                    ->preload()
                    ->relationship('medications', 'description')
                    ->options(Medication::all()->pluck('description', 'id'))
                    ->createOptionForm([
                        TextInput::make('description')
                            ->label('Nombre del medicamento')
                            ->required(),
                        TextInput::make('notes')
                            ->label('Notas'),
                        Forms\Components\Hidden::make('team_id')  // Campo oculto para almacenar el team_id
                            ->default(fn () => Filament::getTenant()->id),
                    ]),
                Toggle::make('active')
                    ->label('Activo')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $diners = [
            'hombre' => 'male',
            'mujer' => 'female',
            'niño' => 'boy',
            'niña' => 'girl',
            'male' => 'hombre',
            'female' => 'mujer',
            'boy' => 'niño',
            'girl' => 'niña',
        ];

        return $table
            ->columns([
                TextColumn::make('full_name')->sortable()->searchable()->label('Nombre Completo'),
                TextColumn::make('dni')
                    ->label('Documento')
                    ->formatStateUsing(function (Beneficiary $record) {
                        return $record->dni_type . '-' . $record->dni;
                    })
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(DB::raw("CONCAT(dni_type, '-', dni)"), 'like', "%{$search}%");
                    }),
                IconColumn::make('active')
                    ->label('Estatus')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('diner')
                    ->label('Comensal')
                    ->options([
                        'male' => 'Hombre',
                        'female' => 'Mujer',
                        'boy' => 'Niño',
                        'girl' => 'Niña',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\Action::make('downloadIdCard')
                    ->label('Descargar Carnet')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Beneficiary $record) {
                        $idCardPath = $record->generateIdCard();
                        return response()->download(storage_path('app/public/' . $idCardPath))
                            ->deleteFileAfterSend(true);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make('table')->label('Tabla')->fromTable(),
                        ExcelExport::make('form')->label('Formulario')->withColumns([
                            Column::make('full_name')->heading('Nombre completo'),
                            Column::make('dni_type')->heading('Tipo de cedula'),
                            Column::make('dni')->heading('Cedula'),
                            Column::make('birthdate')->heading('Fecha de nacimiento'),
                            Column::make('diner')->heading('Tipo de comensal')
                                ->formatStateUsing(function ($state) use ($diners) {
                                    return $diners[$state] ?? '';
                                }),
                            Column::make('address')->heading('Dirección'),
                            Column::make('active')->heading('activo')
                                ->formatStateUsing(function ($state) {
                                    return $state ? 'SI' : 'NO';
                                }),
                            Column::make('phone')->heading('Telefono'),
                            Column::make('alt_phone')->heading('Telefono alternativo'),
                        ]),
                    ])
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'edit' => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }

    protected static function beforeCreate(array $data): array
    {
        return $data;
    }
    
    protected static function afterCreate(array $data): void
    {
        $beneficiary = Beneficiary::where('dni', $data['dni'])->first();
        $beneficiary->generateQrCode();
    }
    
    protected static function beforeSave(array $data): array
    {
        return $data;
    }
    
    protected static function afterSave(array $data): void
    {
        $beneficiary = Beneficiary::where('dni', $data['dni'])->first();
        if ($beneficiary && empty($beneficiary->qr_code)) {
            $beneficiary->generateQrCode();
        }
    }
    
}
