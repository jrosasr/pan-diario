<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\belongsToMany;

class Workday extends Model
{
    protected $fillable = [
        'started_at',
        'ended_at',
        'status',
        'team_id',
    ];

    /**
     * Get the team that owns the Beneficiary
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the beneficiary that owns the Beneficiary
     */
    public function beneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(Beneficiary::class, 'beneficiary_workday', 'workday_id', 'beneficiary_id')
            ->withTimestamps(); // Agrega esto si quieres registrar cuando se creó la relación
    }
}
