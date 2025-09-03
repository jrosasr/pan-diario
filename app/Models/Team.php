<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $guarded = [];

    /**
     * The members that belong to the Team
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id');
    }

    /**
     * Get all of the beneficiaries for the Team
     */
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }

    /**
     * Get all of the beneficiaries for the Team
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Get all of the medications for the Team
     */
    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    /**
     * Get all of the disabilities for the Team
     */
    public function disabilities(): HasMany
    {
        return $this->hasMany(Disability::class);
    }

    /**
     * Get all of the beneficiaries for the Team
     */
    public function workdays(): HasMany
    {
        return $this->hasMany(Workday::class);
    }

    /**
     * Get configuration of the Team
     */
    public function configuration(): HasOne
    {
        return $this->hasOne(Configuration::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function churches(): HasMany
    {
        return $this->hasMany(Church::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
