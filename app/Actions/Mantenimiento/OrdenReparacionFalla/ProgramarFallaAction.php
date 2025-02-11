<?php

namespace App\Actions\Mantenimiento\OrdenReparacionFalla;

use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class ProgramarFallaAction
{
    public function execute(array $data): void
    {
        DB::beginTransaction();

        try {
            $tsql = <<< SQL
            EXECUTE [dbo].[usp_mtto_programar_OT_falla]
                    @id_orden               = :id_orden,
                    @id_orden_falla         = :id_orden_falla,
                    @id_prioridad           = :id_prioridad,
                    @id_usuario_programa    = :id_lider_programa,
                    @id_tecnico_responsable = :id_tecnico_responsable,
                    @tiempo_hh_estimado     = :tiempo_hh_estimado
            SQL;

            $statement = DB::getPdo()->prepare($tsql);
            $statement->bindValue(':id_orden', $data['id_orden'], PDO::PARAM_INT);
            $statement->bindValue(':id_orden_falla', $data['id_orden_falla'], PDO::PARAM_INT);
            $statement->bindValue(':tiempo_hh_estimado', $data['tiempo_hh_estimado']);
            $statement->bindValue(':id_lider_programa', $data['id_usuario_programa'], PDO::PARAM_INT);
            $statement->bindValue(':id_tecnico_responsable', $data['id_tecnico_responsable'], PDO::PARAM_INT);
            $statement->bindValue(':id_prioridad', $data['id_prioridad'], PDO::PARAM_INT);
            $statement->execute();

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
