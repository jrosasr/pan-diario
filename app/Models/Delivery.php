<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'team_id',
        'church_id',
        'notes',
        'delivered',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
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
