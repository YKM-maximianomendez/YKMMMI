<?php

namespace App\Actions\Mantenimiento\OrdenReparacion;

use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class ActualizarOrdenReparacionAction
{
    public function execute(int $id_orden, array $data): void
    {
        DB::beginTransaction();

        try {
            $orden       = $data['orden'];
            $orden_falla = $data['orden_falla'];

            $tsql = <<< SQL
            UPDATE mtto_orden
            SET id_numeroparte = :id_numeroparte,
                id_estacion = :id_estacion,
                id_tipoatencion = :id_tipoatencion,
                operacion = :operacion,
                pzas_terminadas = :pzas_terminadas,
                pzas_requeridas = :pzas_requeridas,
                fecha_requiere_prod = :fecha_requiere_prod
            WHERE 
                (
                    id_orden = :id_orden
                )
            SQL;

            $rowcount = DB::update($tsql, array(
                'id_orden'             => $id_orden,
                'id_numeroparte'       => $orden['id_numeroparte'],
                'id_estacion'          => $orden['id_estacion'],
                'id_tipoatencion'      => $orden['id_tipoatencion'],
                'operacion'            => $orden['operacion'],
                'pzas_terminadas'      => $orden['pzas_terminadas'],
                'pzas_requeridas'      => $orden['pzas_requeridas'],
                'fecha_requiere_prod'  => $orden['fecha_requiere_prod'],
            ));

            if ($rowcount == 0) throw new Exception("Orden no actualizada.");

            $tsql = <<< SQL
            UPDATE mtto_orden_falla
            SET id_falla = :id_falla,
                id_causa = :id_causa,
                observaciones = :observaciones
            WHERE (   id_orden = :id_orden
                AND   tipo = 'U' )
            SQL;

            $rowcount = DB::update($tsql, array(
                'id_orden'         => $id_orden,
                'id_falla'         => $orden_falla['id_falla'],
                'id_causa'         => $orden_falla['id_causa'],
                'observaciones'    => str($orden_falla['observaciones'])->upper(),
            ));

            if ($rowcount == 0) throw new Exception("Falla no actualizada.");

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
