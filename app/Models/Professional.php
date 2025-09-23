<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'identification_number',
        'phone_number',
        'address',
        'team_id',
    ];

    public function specialties()
    {
        return $this->belongsToMany(Specialization::class, 'professional_specialization', 'professional_id', 'specialization_id');
    }

    public function professionalSpecializations()
    {
        return $this->hasMany(ProfessionalSpecialization::class);
    }

    public function professions()
    {
        return $this->hasMany(Profession::class);
    }

    public function professionalServices()
    {
        return $this->hasMany(ProfessionalService::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
