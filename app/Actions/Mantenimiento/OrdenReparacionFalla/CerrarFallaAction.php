<?php

namespace App\Actions\Mantenimiento\OrdenReparacionFalla;

use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class CerrarFallaAction
{
    public function execute(int $id_orden, int $id_orden_falla): void
    {
        DB::beginTransaction();

        try {
            $tsql = <<< SQL
            EXECUTE dbo.usp_mtto_terminar_OT_falla @id_orden_falla = :id_orden_falla
            SQL;

            $statement = DB::getPdo()->prepare($tsql);
            $statement->bindValue(':id_orden_falla', $id_orden_falla, PDO::PARAM_INT);
            $statement->execute();

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
