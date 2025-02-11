<?php

namespace App\Services\Mantenimiento;

use Illuminate\Support\Facades\DB;
use PDO;

class OrdenReparacionMaterialUtilizadoService
{
    public function insertar(int $id_orden, array $data): void
    {
        $tsql = <<< SQL
        EXECUTE dbo.usp_mtto_orden_materialutilizado_insert
                @id_orden			= :id_orden,
                @insumo_refaccion	= :insumo_refaccion,
                @cantidad			= :cantidad,
                @id_usuario_captura = :id_usuario_captura,
                @id_turno_captura	= :id_turno_captura
        SQL;

        $statement = DB::getPdo()->prepare($tsql);

        $statement->bindValue(':id_orden', $id_orden, PDO::PARAM_INT);
        $statement->bindValue(':insumo_refaccion', $data['insumo_refaccion']);
        $statement->bindValue(':cantidad', $data['cantidad']);
        $statement->bindValue(':id_usuario_captura', $data['id_usuario_captura'], PDO::PARAM_INT);
        $statement->bindValue(':id_turno_captura', $data['id_turno_captura'], PDO::PARAM_INT);
        $statement->execute();
    }
}
