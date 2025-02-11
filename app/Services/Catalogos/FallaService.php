<?php

namespace App\Services\Catalogos;

use Illuminate\Support\Facades\DB;

class FallaService
{
    public function consultar(int $id_clasificacion = null, bool $estatus = null): array
    {
        $tsql = "EXECUTE dbo.usp_mtto_falla_select @estatus = ?, @id_clasificacion = ?";
        return DB::select($tsql, array($estatus, $id_clasificacion));
    }

    public function consecutivo(): string
    {
        $consecutivo = DB::table('mtto_falla')->count();
        return 'F' . str_pad(($consecutivo + 1), 3, '0', STR_PAD_LEFT);
    }
}
