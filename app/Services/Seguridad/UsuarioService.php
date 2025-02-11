<?php

namespace App\Services\Seguridad;

use Illuminate\Support\Facades\DB;

class UsuarioService
{
    public function consultar(bool $estatus = true): array
    {
        $tsql = "execute usp_usuarios_select @estatus = ?";
        return DB::select($tsql, array($estatus));
    }
}
