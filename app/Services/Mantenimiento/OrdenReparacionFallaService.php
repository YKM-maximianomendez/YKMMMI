<?php

namespace App\Services\Mantenimiento;

use Exception;
use Illuminate\Support\Facades\DB;
use PDO;

class OrdenReparacionFallaService
{
    public function insertar(int $id_orden, array $orden_falla): int
    {
        $tsql = <<< SQL
        EXECUTE [dbo].[usp_mtto_orden_falla_insert]
                @id_orden           = :id_orden,
                @id_falla           = :id_falla,
                @id_causa           = :id_causa,
                @observaciones      = :observaciones,
                @tipo               = :tipo,
                @id_usuario_registro = :id_usuario_registro,
                @id_turno_captura   = :id_turno_captura,
                @id_orden_falla	    = :id_orden_falla
        SQL;

        $statement = DB::getPdo()->prepare($tsql);
        $statement->bindValue(':id_orden', $id_orden, PDO::PARAM_INT);
        $statement->bindValue(':id_falla', $orden_falla['id_falla'], PDO::PARAM_INT);
        $statement->bindValue(':id_causa', $orden_falla['id_causa'], PDO::PARAM_INT | PDO::PARAM_NULL);
        $statement->bindValue(':observaciones', str($orden_falla['observaciones'])->upper());
        $statement->bindValue(':tipo', $orden_falla['tipo']);
        $statement->bindValue(':id_usuario_registro', $orden_falla['id_usuario_registro'], PDO::PARAM_INT | PDO::PARAM_NULL);
        $statement->bindValue(':id_turno_captura', $orden_falla['id_turno'], PDO::PARAM_INT);
        $statement->bindParam(':id_orden_falla', $id_orden_falla, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->execute();

        return (int) $id_orden_falla;
    }

    public function consultar_por_estatus(string $orden_falla_estatus, ?int $id_usuario = null): array
    {
        $tsql = "EXECUTE usp_mtto_consultar_ordenes_fallas @id_estatus_falla = ?, @id_usuario = ?";

        $id_estatus = null;
        switch ($orden_falla_estatus) {
            case 'E':
                $id_estatus = 1;
                break;
            case 'P':
                $id_estatus = 3;
                break;
            case 'T':
                $id_estatus = 4;
                break;
            default:
                throw new Exception("Filtro no válido");
                break;
        }

        return DB::select($tsql, array($id_estatus, $id_usuario));
    }

    public function consultar_fallas_programadas(): array
    {
        $tsql = "SELECT p.id_orden_falla,
            p.id_orden,
            p.no_falla,
            p.falla_general,
            p.falla_tecnico_responsable,
            p.falla_num_reparaciones,
            p.falla_num_reparaciones_pendientes,
            p.falla_prioridad_nivel,
            p.falla_prioridad,
            p.falla_tiempo_hh_real_total
        FROM (   SELECT mf.id_orden_falla,
                        mf.id_orden,
                        mf.no_falla,
                        mf.falla_codigo_falla + ' - ' + mf.falla_falla AS falla_general,
                        mf.falla_tecnico_responsable,
                        mf.falla_num_reparaciones,
                        mf.falla_num_reparaciones_pendientes,
                        mf.falla_prioridad_nivel,
                        mf.falla_prioridad,
                        ISNULL(mf.falla_tiempo_hh_real_total, 0) AS falla_tiempo_hh_real_total,
                        1 AS orden
                    FROM dbo.v_mtto_ordenes_fallas AS mf
                    WHERE (mf.falla_tipo IN ( 'U', 'A' ))
                    AND (   mf.orden_id_estatus IN ( 1 )
                        AND   mf.falla_id_estatus IN ( 3 ))
                    AND mf.falla_id_prioridad <> 0
                UNION ALL
                SELECT mf.id_orden_falla,
                        mf.id_orden,
                        mf.no_falla,
                        mf.falla_codigo_falla + ' - ' + mf.falla_falla AS falla_general,
                        mf.falla_tecnico_responsable,
                        mf.falla_num_reparaciones,
                        mf.falla_num_reparaciones_pendientes,
                        mf.falla_prioridad_nivel,
                        mf.falla_prioridad,
                        ISNULL(mf.falla_tiempo_hh_real_total, 0) AS falla_tiempo_hh_real_total,
                        2 AS orden
                    FROM dbo.v_mtto_ordenes_fallas AS mf
                    WHERE (mf.falla_tipo IN ( 'U', 'A' ))
                    AND (   mf.orden_id_estatus IN ( 1 )
                        AND   mf.falla_id_estatus IN ( 3 ))
                    AND mf.falla_prioridad_nivel = 0) p
        ORDER BY p.orden ASC,
                p.falla_prioridad_nivel ASC";

        return DB::select($tsql);
    }

    public function contador(int $id_usuario = null): array
    {
        $result = [
            'fallas_programadas' => 0,
            'fallas_emitidas'    => 0,
            'fallas_terminadas'  => 0
        ];

        $statement = DB::getPdo()->prepare(<<< SQL
        EXECUTE dbo.usp_mtto_ordenes_fallas_contador
                @id_usuario         = ?,
                @fallas_emitidas    = ?,
                @fallas_programadas = ?,
                @fallas_terminadas  = ?
        SQL);

        $statement->bindValue(1, $id_usuario, PDO::PARAM_INT | PDO::PARAM_NULL);
        $statement->bindParam(2, $result['fallas_emitidas'], PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->bindParam(3, $result['fallas_programadas'], PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->bindParam(4, $result['fallas_terminadas'], PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->execute();

        return $result;
    }
}
