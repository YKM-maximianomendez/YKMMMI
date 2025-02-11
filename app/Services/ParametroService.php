<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ParametroService
{
    public function obtener_valor(string $parametro, string $modulo): ?string
    {
        $param = DB::selectOne("SELECT valor FROM general_parametros WHERE parametro=? AND modulo=?", array($parametro, $modulo));
        return $param?->valor;
    }
}
