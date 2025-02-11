<?php

namespace App\Services\Mantenimiento;

use Illuminate\Support\Facades\DB;

class OrdenReparacionActividadReparacionService
{
    public function insertar(int $id_orden_falla, array $actividad): void
    {
        $tsql = <<< SQL
        EXECUTE dbo.usp_mtto_orden_falla_act_reparacion_insert
            @id_orden_falla			= :id_orden_falla,
            @id_tecnico_reparador	= :id_tecnico_reparador,
            @id_act_reparacion		= :id_act_reparacion,
            @fecha					= :fecha,
            @tiempo_hh				= :tiempo_hh,
            @observaciones			= :observaciones,
            @terminada				= :terminada,
            @id_turno_captura		= :id_turno_captura
        SQL;
 
        $statement = DB::getPdo()->prepare($tsql);
        $statement->execute([
            ':id_orden_falla'        => $id_orden_falla,
            ':id_tecnico_reparador'  => $actividad['id_tecnico_reparador'],
            ':id_act_reparacion'     => $actividad['id_act_reparacion'],
            ':fecha'                 => $actividad['fecha'],
            ':tiempo_hh'             => $actividad['tiempo_hh'],
            ':observaciones'         => $actividad['observaciones'],
            ':terminada'             => $actividad['terminada'],
            ':id_turno_captura'      => $actividad['id_turno_captura']
        ]);
    }
}
