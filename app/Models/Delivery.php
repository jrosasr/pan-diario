<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Delivery extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'team_id',
        'church_id',
        'beneficiary_id',
        'notes',
        'delivered',
        'signature_beneficiary',
        'signature_deliverer',
        'deliverer_name',
        'deliverer_dni',
        'delivered_at',
        'men_count',
        'women_count',
        'boys_count',
        'girls_count'
    ];

    protected $casts = [
        'delivered' => 'boolean',
        'delivered_at' => 'datetime',
    ];

    // Configuración de Media Library para imágenes de entrega
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    // beneficiary_id
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'delivery_resource')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // Relación para el Repeater en Filament
    public function deliveryResources()
    {
        return $this->hasMany(DeliveryResource::class);
    }
}
