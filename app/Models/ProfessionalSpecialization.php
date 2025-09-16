<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalSpecialization extends Model
{
    protected $fillable = [
        'professional_id',
        'specialization_id',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
}
