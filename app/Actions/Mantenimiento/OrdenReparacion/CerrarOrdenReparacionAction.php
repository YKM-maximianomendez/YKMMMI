<?php

namespace App\Actions\Mantenimiento\OrdenReparacion;

use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class CerrarOrdenReparacionAction
{
    public function execute(int $id_orden, array $data, array $evidencias): void
    {
        DB::beginTransaction();

        try {
            $tsql = <<< SQL
            EXECUTE dbo.usp_mtto_cerrar_OT_1
                    @id_orden                = :id_orden,
                    @fecha_inicia_reparacion = :fecha_inicia_reparacion,
                    @fecha_fin_reparacion    = :fecha_fin_reparacion
            SQL;

            $statement = DB::getPdo()->prepare($tsql);
            
            $statement->bindValue(':id_orden', $id_orden, PDO::PARAM_INT);
            $statement->bindValue(':fecha_inicia_reparacion', $data['fecha_inicia_reparacion']);
            $statement->bindValue(':fecha_fin_reparacion', $data['fecha_fin_reparacion']);
            $statement->execute();

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
