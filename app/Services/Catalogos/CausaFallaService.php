<?php

namespace App\Services\Catalogos;

use Illuminate\Support\Facades\DB;

class CausaFallaService
{
    public function consultar(bool $estatus = null): array
    {
        $tsql = "EXECUTE usp_mtto_causafalla_select @estatus = ?";
        return DB::select($tsql, array($estatus));
    }

    public function consecutivo(): string
    {
        $consecutivo = DB::table('mtto_causa_falla')->count();
        return 'C' . str_pad(($consecutivo + 1), 3, '0', STR_PAD_LEFT);
    }
}
