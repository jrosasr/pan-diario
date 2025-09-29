<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'request_type',
        'description',
        'petition_type',
        'date',
        'status',
        'appointment_date',
        'team_id'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'date' => 'date'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
