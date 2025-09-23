<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalService extends Model
{
    protected $fillable = [
        'service_id',
        'professional_id',
        'limit',
        'is_free',
        'price',
        'discount_percentage',
        'discount_amount',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
