<?php

namespace App\Http\Controllers\Mantenimiento\KioscoReparaciones;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultarReparacionesPendientesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Tecnico Reparador']);
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden_falla)
    {
        $tsql = "SELECT mof.id_orden_falla, mofar.id_actividad_reparacion, mofar.fecha, mar.codigo+' - '+mar.descripcion AS reparacion_pendiente, u.nombre AS tecnico_reparador, mofar.fecha_captura, mofar.observaciones, mof.id_prioridad
        FROM dbo.mtto_orden_falla_act_reparacion mofar
            INNER JOIN dbo.mtto_orden_falla mof ON(mofar.id_orden_falla=mof.id_orden_falla)
            INNER JOIN dbo.mtto_act_reparacion mar ON(mofar.id_act_reparacion=mar.id_act_reparacion)
            INNER JOIN dbo.usuarios u ON(u.id=mofar.id_tecnico_reparador)
        WHERE(mofar.terminada=0)AND(ISNULL(mofar.contabilizado, 0)=0)AND(mof.id_orden_falla=?)
        ORDER BY mof.id_prioridad ASC";

        $resultset = DB::select($tsql, array($id_orden_falla));

        if ($request->ajax()) {
            Carbon::setLocale('es');

            return datatables($resultset)
                ->addColumn('tiempo_abierta', function ($row) {
                    return now()->parse($row->fecha_captura)->diffForHumans(now());
                })
                ->toJson();
        }
    }
}
