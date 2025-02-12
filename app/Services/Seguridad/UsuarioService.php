<?php

namespace App\Services\Seguridad;

use Illuminate\Support\Facades\DB;

class UsuarioService
{
    const DEFAULT_PASSWORD = 'User123';

    public function consultar(bool $estatus = true): array
    {
        $tsql = "execute usp_usuarios_select @estatus = ?";
        return DB::select($tsql, array($estatus));
    }
}
