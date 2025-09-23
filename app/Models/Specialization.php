<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $fillable = [
        'name',
        'team_id',
    ];

    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'professional_specialization', 'specialization_id', 'professional_id');
    }

    public function professions()
    {
        return $this->belongsToMany(Profession::class, 'profession_specialization', 'specialization_id', 'profession_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
