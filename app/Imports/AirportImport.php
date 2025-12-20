<?php

namespace App\Imports;

use App\Models\Airport;
use Maatwebsite\Excel\Concerns\ToModel;

class AirportImport implements ToModel
{
    public function model(array $row)
    {
        if ($row[1] != 'CITYCODE' && $row[0] != null) {
            return Airport::create([
                'city' => $row[0],
                'city_code' => $row[1],
                'country_code' => $row[2],
                'country' => $row[3],
                'airport_code' => $row[4],
                'airport' => $row[5],
            ]);
        }
    }
}
