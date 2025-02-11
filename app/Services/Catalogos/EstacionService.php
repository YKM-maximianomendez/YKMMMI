<?php

namespace App\Services\Catalogos;

use Illuminate\Support\Facades\DB;

class EstacionService
{
    public function consultar(bool $estatus = null): array
    {
        $tsql = "EXECUTE usp_manufactura_estacion_select @estatus = ?";
        return DB::select($tsql, array($estatus));
    }

    public function consultar_numerosdeparte(int $id_estacion): array
    {
        $tsql = <<< SQL
        SELECT
            np.numeroparte,
            np.nombre,
            np.modelos,
            np.estatus_desc,
            np.estatus
        FROM
            dbo.manufactura_estacion_numeroparte  AS enp
            INNER JOIN
                dbo.manufactura_estacion          AS me
                    ON (enp.id_estacion = me.id_estacion)
            INNER JOIN
                dbo.v_manufactura_numerosodeparte AS np
                    ON (enp.id_numeroparte = np.id_numeroparte)
        WHERE (
                me.id_estacion = ?
              )
        SQL;

        return DB::select($tsql, array($id_estacion));
    }
}
