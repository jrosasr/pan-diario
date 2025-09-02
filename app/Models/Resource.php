<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'quantity',
        'expiration_date',
        'unit_of_measure',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
