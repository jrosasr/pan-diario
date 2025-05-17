<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BeneficiaryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error('No hay usuario autenticado durante la importación.');
            return null;
        }

        $team = $user->currentTeam();
        if (!$team) {
            \Log::error('El usuario no tiene un equipo actual.');
            return null;
        }

        $teamId = $team->id;

        // Convertir la fecha de Excel a formato YYYY-MM-DD
        if (is_numeric($row['fecha_de_nacimiento'])) {
            $birthdate = Date::excelToDateTimeObject($row['fecha_de_nacimiento'])->format('Y-m-d');
        } else {
            $birthdate = $row['fecha_de_nacimiento'];
            \Log::warning("Formato de fecha inválido: " . $row['fecha_de_nacimiento']);
        }

        // Convertir 'Si'/'No' a booleano
        $active = strtolower($row['activo']) == 'si' ? true : false;

        $diners = [
            'hombre' => 'male',
            'mujer' => 'female',
            'niño' => 'boy',
            'niña' => 'girl',
        ];

        $beneficiaryData = [
            'full_name' => $row['nombre_completo'],
            'dni_type' => $row['tipo_de_cedula'] ?? null,
            'dni' => $row['cedula'],
            'birthdate' => $birthdate,
            'diner' => $diners[strtolower($row['tipo_de_comensal'] ?? 'hombre')],
            'active' => $active,
            'address' => $row['direccion'] ?? null,
            'phone' => $row['telefono'] ?? null,
            'alt_phone' => $row['Telefono_alternativo'] ?? null,
            'team_id' => $teamId,
        ];

        $beneficiary = Beneficiary::updateOrCreate(
            ['dni' => $row['cedula'], 'team_id' => $teamId],
            $beneficiaryData
        );

        return $beneficiary;
    }
}