<?php

namespace App\Actions\Mantenimiento\OrdenReparacion;

use App\Services\EvidenciaFileSystemService;
use App\Services\Mantenimiento\EvidenciaOrdenReparacionService;
use App\Services\TurnoService;
use Illuminate\Support\Facades\DB;
use Throwable;

class RegistrarEvidenciasAction
{
    public function __construct(private readonly EvidenciaOrdenReparacionService $evidencia_orden_reparacion_service) {}

    public function execute(int $id_orden, array $evidencias): void
    {
        DB::beginTransaction();

        try {
            $id_turno = (new TurnoService())->consultar_turno();
            $evidencia_fs = new EvidenciaFileSystemService();

            foreach ($evidencias as $evidencia) {
                $evidencia['url'] = "";
                $evidencia['id_turno'] = $id_turno;

                if ($evidencia_fs->store($id_orden, $evidencia)) {
                    $this->evidencia_orden_reparacion_service->insertar($id_orden, $evidencia);
                }
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
