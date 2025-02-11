<?php

namespace App\Actions\Mantenimiento\OrdenReparacionFalla;

use App\Services\Mantenimiento\OrdenReparacionActividadReparacionService;
use App\Services\TurnoService;
use Illuminate\Support\Facades\DB;
use Throwable;

class RegistrarActividadesReparacionAction
{
    public function __construct(private readonly OrdenReparacionActividadReparacionService $actividad_reparacion_service) {}

    public function execute(int $id_orden_falla, array $actividades_reparacion): void
    {
        DB::beginTransaction();

        try {
            $id_turno = (new TurnoService())->consultar_turno();

            foreach ($actividades_reparacion as $actividad_reparacion) {
                $actividad_reparacion['id_turno_captura'] = $id_turno;
                $this->actividad_reparacion_service->insertar($id_orden_falla, $actividad_reparacion);
            }
            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
