<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TripulacionService
{
    public function consultar(): array
    {
        return DB::select("SELECT id_tripulacion, tripulacion FROM general_tripulacion");
    }
}
