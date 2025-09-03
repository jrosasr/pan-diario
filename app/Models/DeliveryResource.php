<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryResource extends Model
{
    protected $table = 'delivery_resource';

    protected $fillable = [
        'delivery_id',
        'resource_id',
        'quantity',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
