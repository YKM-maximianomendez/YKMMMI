<?php

namespace App\Actions\Mantenimiento\OrdenReparacionFalla;

use App\Services\EvidenciaFileSystemService;
use App\Services\Mantenimiento\EvidenciaOrdenReparacionService;
use App\Services\Mantenimiento\OrdenReparacionFallaService;
use App\Services\TurnoService;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class InsertarOrdenReparacionFallaAction
{
    public function __construct(
        private readonly TurnoService $turno_service,
        private readonly OrdenReparacionFallaService $orden_reparacion_falla_service,
        private readonly EvidenciaOrdenReparacionService $evidencia_orden_reparacion_service
    ) {}


    public function execute(int $id_orden, array $data):void {
        DB::beginTransaction();

        try {
            $orden_falla      = $data['orden_falla'];
            $orden_evidencias = $data['evidencias'];

            $id_turno = $this->turno_service->consultar_turno();

            $orden['id_turno']       = $id_turno;
            $orden_falla['id_turno'] = $id_turno;

            $id_orden_falla = $this->orden_reparacion_falla_service->insertar($id_orden, $orden_falla);

            if (empty($id_orden_falla)) {
                throw new Exception("ID de orden de reparación (falla) no generado.");
            }

            if (count($orden_evidencias) > 0) {
                $evidencia_fs = new EvidenciaFileSystemService();

                foreach ($orden_evidencias as $evidencia) {
                    $evidencia['url'] = "";
                    $evidencia['id_turno'] = $id_turno;

                    if ($evidencia_fs->store($id_orden, $evidencia)) {
                        $this->evidencia_orden_reparacion_service->insertar($id_orden, $evidencia);
                    }
                }
            }

            DB::commit();

        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }        
    }
}
