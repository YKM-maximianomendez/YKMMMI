<?php

namespace App\Services\Catalogos;

use Illuminate\Support\Facades\DB;

class NumeroParteService
{
    public function consultar(int $id_modelo = null, bool $estatus = null): array
    {
        $tsql = "EXECUTE usp_manufactura_numeroparte_select @estatus = ?, @id_modelo = ?";
        return DB::select($tsql, array($estatus, $id_modelo));
    }
}
