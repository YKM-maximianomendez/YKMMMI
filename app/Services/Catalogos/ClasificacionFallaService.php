<?php

namespace App\Services\Catalogos;

use Illuminate\Support\Facades\DB;

class ClasificacionFallaService
{
    public function consultar(bool $estatus = null): array
    {
        $tsql = "EXECUTE usp_mtto_clasificacionfalla_select @estatus = ?";
        return DB::select($tsql, array($estatus));
    }
}
