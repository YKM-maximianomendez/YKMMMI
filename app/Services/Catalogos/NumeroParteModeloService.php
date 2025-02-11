<?php

namespace App\Services\Catalogos;

use Illuminate\Support\Facades\DB;

class NumeroParteModeloService
{
    public function consultar(bool $estatus = null): array
    {
        $tsql = "EXECUTE usp_manufactura_numeroparte_modelo_select @estatus = ?";
        return DB::select($tsql, array($estatus));
    }
}
