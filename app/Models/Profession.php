<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $fillable = [
        'name',
        'team_id',
    ];

    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'professional_professions', 'profession_id', 'professional_id');
    }

    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'profession_specialization', 'profession_id', 'specialization_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
