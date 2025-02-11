<?php

namespace App\Services\Mantenimiento;

use Illuminate\Support\Facades\DB;

class EvidenciaOrdenReparacionService
{
    public function insertar(int $id_orden, array $evidencia): void
    {
        $tsql = <<< SQL
        EXECUTE dbo.usp_mtto_orden_evidencia_insert
                @id_orden           = :id_orden,
                @tipo               = :tipo,
                @nombre             = :nombre,
                @mime               = :mime,
                @url                = :url,
                @id_usuario_captura = :id_usuario_captura,
                @id_turno_captura   = :id_turno_captura
        SQL;

        $statement = DB::getPdo()->prepare($tsql);
        $statement->execute([
            ':id_orden'             => $id_orden,
            ':tipo'                 => $evidencia['tipo_evidencia'],
            ':nombre'               => $evidencia['nombre_archivo'],
            ':mime'                 => $evidencia['MIME'],
            ':url'                  => $evidencia['url'],
            ':id_usuario_captura'   => $evidencia['id_usuario_captura'],
            ':id_turno_captura'     => $evidencia['id_turno']
        ]);
    }
}
