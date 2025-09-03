<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'pastor_name',
        'identification_number',
        'phone_number',
        'address',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
