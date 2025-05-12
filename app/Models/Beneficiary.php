<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\belongsToMany;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

use Intervention\Image\Laravel\Facades\Image; // Cambiado a Laravel facade

class Beneficiary extends Model
{
    protected $fillable = [
        'full_name',
        'dni',
        'birthdate',
        'weight',
        'address',
        'photo',
        'dni_photo',
        'phone',
        'alt_phone',
        'diner',
        'qr_code',
        'active',
    ];

    protected static function booted()
    {
        static::created(function ($model) {
            $model->generateQrCode();
        });

        static::updated(function ($model) {
            if (empty($model->qr_code)) {
                $model->generateQrCode();
            }
        });
    }

    /**
     * Get the team that owns the Beneficiary
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the treatments that owns the Beneficiary
     */
    public function treatments()
    {
        return $this->belongsToMany(Treatment::class, 'beneficiary_treatment', 'beneficiary_id', 'treatment_id');
    }

    /**
     * Get the workdays that owns the Beneficiary
     */
    public function workdays()
    {
        return $this->belongsToMany(Workday::class, 'beneficiary_workday', 'beneficiary_id', 'workday_id');
    }

    public function generateQrCode()
    {
        // Crear directorio si no existe
        Storage::disk('public')->makeDirectory('qrcodes');

        $uniqueId = $this->id .'_'. $this->dni ?? uniqid(); // Usar DNI si está disponible, sino generar un UUID

        $qrData = [
            'id' => $this->id,
            'name' => $this->full_name,
            'dni' => $this->dni ?? 'N/A',
            'timestamp' => now()->toDateTimeString()
        ];

        $filename = 'beneficiary_'.$this->id.'_'.Str::slug($this->dni).'.png';
        $path = 'qrcodes/'.$filename;

        // Generar QR
        QrCode::format('png')
            ->size(300)
            ->generate(json_encode($qrData), storage_path('app/public/'.$path));

        // Guardar solo la ruta relativa
        $this->qr_code = $path;
        $this->saveQuietly(); // Evita ciclos de actualización
    }

    public function generateIdCard()
    {        
        $currentTeam = Auth::user()->currentTeam();

        // Ruta del logo
        $logoPath = $currentTeam->logo ?? public_path('logo.png');
        
        // Crear directorio si no existe
        Storage::disk('public')->makeDirectory('idcards');
        
        // Tamaño del carnet (horizontal - ejemplo: 850x540 px)
        $width = 850;
        $height = 540;
        
        // Crear imagen base (blanco)
        $img = \Intervention\Image\Laravel\Facades\Image::create($width, $height)->fill('#ffffff');
        
        // Agregar logo (ajustar tamaño)
        if (file_exists($logoPath)) {
            $logo = \Intervention\Image\Laravel\Facades\Image::read($logoPath)->scale(175); // Escalar manteniendo aspecto
            $img->place($logo, 'top-left', 20, 20); // Posicionar logo
        }
        
        // Agregar QR code
        if ($this->qr_code && Storage::disk('public')->exists($this->qr_code)) {
            $qr = \Intervention\Image\Laravel\Facades\Image::read(storage_path('app/public/' . $this->qr_code))->scale(250);
            $img->place($qr, 'top-right', 20, 20);
        }

        // Encabezado de la Congregacion
        $img->text("Comedor", 200, 95, function($font) {
            $font->file(public_path('fonts/CascadiaMono.ttf'));
            $font->size(25);
            $font->color('#333333');
        });

        $img->text($currentTeam->name, 200, 130, function($font) {
            $font->file(public_path('fonts/CascadiaMono.ttf'));
            $font->size(25);
            $font->color('#333333');
        });
        
        // CORRECCIÓN: Método correcto para agregar texto
        $img->text("Nro: #" . $this->id, 50, 300, function($font) {
            $font->file(public_path('fonts/CascadiaMono.ttf'));
            $font->size(35);
            $font->color('#333333');
        });

        $img->text("DNI: " . $this->dni, 50, 350, function($font) {
            $font->file(public_path('fonts/CascadiaMono.ttf'));
            $font->size(35);
            $font->color('#333333');
        });

        $img->text($this->full_name, 50, 375, function($font) {
            $font->file(public_path('fonts/CascadiaMono.ttf')); // Usar 'file' en lugar de 'filename'
            $font->size(35);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
        });

        $img->text($currentTeam->address, 50, 500, function($font) {
            $font->file(public_path('fonts/CascadiaMono.ttf'));
            $font->size(20);
            $font->color('#333333');
        });
        
        // Guardar la imagen
        $filename = 'idcards/beneficiary_'.$this->id.'_'.Str::slug($this->dni).'.png';
        $img->save(storage_path('app/public/'.$filename));
        
        return $filename;
    }
}
