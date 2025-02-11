<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PrioridadService
{
    public function consultar(): array
    {
        return DB::select("SELECT id_prioridad, prioridad FROM mtto_prioridad_falla WHERE id_prioridad IN (1, 2, 3) ORDER BY nivel ASC");
    }
}
