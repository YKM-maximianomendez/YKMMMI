<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TabuladorTmpService
{
    public function consultar(): array
    {
        return DB::select("SELECT fracc, minutos FROM dbo.general_tabulador_tmp WHERE minutos NOT IN (60)");
    }
}
