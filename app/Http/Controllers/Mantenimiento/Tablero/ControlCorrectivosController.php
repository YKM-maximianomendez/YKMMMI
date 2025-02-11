<?php

namespace App\Http\Controllers\Mantenimiento\Tablero;

use App\Http\Controllers\Controller;
use App\Services\Mantenimiento\OrdenReparacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControlCorrectivosController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, OrdenReparacionService $service)
    {
        if ($request->ajax()) {
            $tsql = "SELECT mf.no_falla, mf.falla_prioridad_nivel, mf.falla_prioridad, mf.orden_estacion, mf.orden_numeroparte, mf.orden_operacion, mf.falla_codigo_falla, mf.falla_falla, mf.falla_tecnico_responsable, ISNULL(mf.falla_tiempo_hh_estimado, 0) AS falla_tiempo_hh_estimado, ISNULL(mf.falla_tiempo_hh_real_total, 0) AS falla_tiempo_hh_real_total, mf.orden_estatus, mf.falla_estatus, mf.orden_fecha_requiere_prod, mf.falla_tiempo_hh_real
            FROM dbo.v_mtto_ordenes_fallas AS mf
            WHERE(mf.falla_tipo IN ('U', 'A'))AND(mf.orden_id_estatus IN (1))
            ORDER BY mf.falla_prioridad ASC";

            $resultset = DB::select($tsql);

            return datatables($resultset)
                ->with(['contador' => $service->contador()])
                ->toJson();
        }

        return view('mantenimiento.tablero.control-correctivos.index');
    }
}
