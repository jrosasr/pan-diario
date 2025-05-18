<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'multiple_beneficiaries_for_workday',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
