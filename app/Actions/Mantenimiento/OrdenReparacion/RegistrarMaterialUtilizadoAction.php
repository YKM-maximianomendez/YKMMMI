<?php

namespace App\Actions\Mantenimiento\OrdenReparacion;

use App\Services\Mantenimiento\OrdenReparacionMaterialUtilizadoService;
use App\Services\TurnoService;
use Illuminate\Support\Facades\DB;
use Throwable;

class RegistrarMaterialUtilizadoAction
{
    public function __construct(private readonly OrdenReparacionMaterialUtilizadoService $material_utilizado_service) {}

    public function execute(int $id_orden, array $material_utilizado): void
    {
        DB::beginTransaction();
        
        try {
            $id_turno = (new TurnoService())->consultar_turno();

            foreach ($material_utilizado as $insumo) {
                $insumo['id_turno_captura'] = $id_turno;
                $this->material_utilizado_service->insertar($id_orden, $insumo);
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
