<?php

namespace App\Services\Catalogos;

use Illuminate\Support\Facades\DB;

class ActividadReparacionService
{
    public function consultar(bool $estatus = null): array
    {
        $tsql = "EXECUTE dbo.usp_mtto_act_reparacion_select @estatus = ?";
        return DB::select($tsql, array($estatus));
    }

    public function consecutivo(): string
    {
        $consecutivo = DB::table('mtto_act_reparacion')->count();
        return 'A' . str_pad(($consecutivo + 1), 3, '0', STR_PAD_LEFT);
    }
}
