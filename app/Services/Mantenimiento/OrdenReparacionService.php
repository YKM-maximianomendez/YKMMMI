<?php

namespace App\Services\Mantenimiento;

use Illuminate\Support\Facades\DB;
use PDO;

class OrdenReparacionService
{
    public function insertar(array $orden): int
    {
        $tsql = <<< SQL
        EXECUTE dbo.usp_mtto_orden_insert
                @id_numeroparte         = :id_numeroparte,
                @id_estacion            = :id_estacion,
                @id_tipoatencion        = :id_tipoatencion,
                @id_tipomantenimiento   = :id_tipomantenimiento,
                @operacion              = :operacion,
                @id_usuario_registro    = :id_usuario_registro,
                @pzas_terminadas        = :pzas_terminadas,
                @pzas_requeridas        = :pzas_requeridas,
                @fecha_requiere_prod    = :fecha_requiere_prod,
                @id_turno_emision       = :id_turno_emision,
                @id_orden               = :id_orden
        SQL;

        $statement = DB::getPdo()->prepare($tsql);
        $statement->bindValue(':id_numeroparte', $orden['id_numeroparte'], PDO::PARAM_INT);
        $statement->bindValue(':id_estacion', $orden['id_estacion'], PDO::PARAM_INT);
        $statement->bindValue(':id_tipoatencion', $orden['id_tipoatencion'], PDO::PARAM_INT);
        $statement->bindValue(':id_tipomantenimiento', $orden['id_tipomantenimiento'], PDO::PARAM_INT);
        $statement->bindValue(':operacion', $orden['operacion']);
        $statement->bindValue(':id_usuario_registro', $orden['id_usuario_registro'], PDO::PARAM_INT);
        $statement->bindValue(':pzas_terminadas', $orden['piezas_terminadas'], PDO::PARAM_INT);
        $statement->bindValue(':pzas_requeridas', $orden['piezas_requeridas'], PDO::PARAM_INT);
        $statement->bindValue(':fecha_requiere_prod', $orden['fecha_requiere_prod']);
        $statement->bindValue(':id_turno_emision', $orden['id_turno'], PDO::PARAM_INT);
        $statement->bindParam(':id_orden', $id_orden, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->execute();

        return (int) $id_orden;
    }

    public function consultar_por_estatus(int $id_estatus_orden, int $id_usuario = null): array
    {
        $tsql = "EXECUTE usp_mtto_consultar_ordenes_por_estatus @id_estatus_orden = ?, @id_usuario = ?";
        return DB::select($tsql, array($id_estatus_orden, $id_usuario));
    }

    public function consultar_por_id(int $id_orden): ?object
    {
        $tsql = "SELECT
            mo.id_orden,
            mo.no_orden,
            mo.orden_id_numeroparte,
            mo.orden_id_estacion,
            mo.orden_id_tipoatencion,
            mo.orden_id_tipomantenimiento,
            mo.orden_id_turno_emision,
            mo.orden_id_estatus,
            mo.orden_tipoatencion,
            mo.orden_estacion,
            mo.orden_numeroparte,
            mo.orden_operacion,
            mo.orden_tipomantenimiento,
            mo.orden_pzas_requeridas,
            mo.orden_pzas_terminadas,
            mo.orden_fecha_requiere_prod,
            mo.orden_fecha_emision,
            mo.orden_fecha_cierre_mtto,
            mo.orden_fecha_cierre_prensas,
            mo.orden_turno,
            mo.orden_estatus,
            mo.orden_id_usuario_mtto_cierra_orden,
            mo.orden_usuario_mtto_cierra_orden,
            mo.orden_id_usuario_prensas_cierra_orden,
            mo.orden_usuario_prensas_cierra_orden,
            mo.orden_num_fallas,
            mo.orden_f_inicio_reparacion,
            mo.orden_f_fin_reparacion,
            p1.nombre AS orden_usuario_registro,
            mo.orden_FORMA
        FROM
            v_mtto_ordenes   AS mo
            JOIN
                dbo.usuarios AS p1
                    ON (mo.orden_id_usuario_registro = p1.id)
        WHERE
            (mo.id_orden = ?)";

        return DB::selectOne($tsql, array($id_orden));
    }

    public function contador(int $id_usuario = null): array
    {
        $result = [
            'ordenes_abiertas'          => 0,
            'ordenes_cerradas_mtto'     => 0,
            'ordenes_cerradas_prensas'  => 0
        ];

        $statement = DB::getPdo()->prepare(<<< SQL
        EXECUTE dbo.usp_mtto_ordenes_contador
                @id_usuario                 = ?,
                @ordenes_abiertas           = ?,
                @ordenes_cerradas_mtto      = ?,
                @ordenes_cerradas_prensas   = ?
        SQL);

        $statement->bindValue(1, $id_usuario, PDO::PARAM_INT | PDO::PARAM_NULL);
        $statement->bindParam(2, $result['ordenes_abiertas'], PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->bindParam(3, $result['ordenes_cerradas_mtto'], PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->bindParam(4, $result['ordenes_cerradas_prensas'], PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        $statement->execute();

        return $result;
    }
}
