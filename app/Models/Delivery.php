<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
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
    ];

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
