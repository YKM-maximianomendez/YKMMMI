<?php

namespace App\Actions\Mantenimiento\OrdenReparacion;

use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class ConfirmarCierreOrdenReparacionAction
{
    public function execute(int $id_orden, array $data): void
    {
        DB::beginTransaction();

        try {
            $tsql = <<< SQL
            EXECUTE dbo.usp_mtto_cerrar_OT_2
                    @id_orden           = :id_orden,
                    @id_usuario_prensas = :id_usuario_prensas
            SQL;

            $statement = DB::getPdo()->prepare($tsql);
            
            $statement->bindValue(':id_orden', $id_orden, PDO::PARAM_INT);
            $statement->bindValue(':id_usuario_prensas', $data['id_usuario_prensas'], PDO::PARAM_INT);
            $statement->execute();

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
