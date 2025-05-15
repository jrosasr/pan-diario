<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UniqueDniForTeam implements ValidationRule
{
    private $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $teamId = Auth::user()->currentTeam()->id;

        $query = DB::table('beneficiaries')
            ->where('dni', $value)
            ->where('team_id', $teamId);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->count() > 0) {
            $fail('El documento de identidad ya está en uso para este equipo.');
        }
    }
}
