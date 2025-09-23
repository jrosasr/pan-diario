<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'description',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function professionalServices()
    {
        return $this->hasMany(ProfessionalService::class);
    }
}
