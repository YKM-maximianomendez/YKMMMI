<?php

namespace App\Http\Controllers\Mantenimiento\Reportes;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\EstacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrdenesReparacionController extends Controller
{
    public function __construct(
        private readonly EstacionService $estacion_service
    ) {}

    public function index()
    {
        if (request()->ajax()) {
            try {
                $tsql = <<< SQL
                EXECUTE usp_reportes_consultar_ordenesreparacion
                    @orden_id_estacion			= ?,
                    @orden_id_numeroparte		= ?,
                    @orden_id_tipoatencion		= ?,
                    @orden_id_turno				= ?,
                    @orden_id_usuario_emitio	= ?,
                    @orden_fecha_emision_inicio	= ?,
                    @orden_fecha_emision_fin	= ?
                SQL;

                $resultset = DB::select($tsql, array(
                    request()->input('orden_id_estacion'),
                    request()->input('orden_id_numeroparte'),
                    request()->input('orden_id_tipoatencion'),
                    request()->input('orden_id_turno'),
                    request()->input('orden_id_usuario_capturo'),
                    request()->input('orden_fecha_emision_inicio', now()->toDateString()),
                    request()->input('orden_fecha_emision_fin', now()->toDateString()),
                ));

                return datatables($resultset)
                    ->toJson();
            } catch (Throwable $th) {
                dd($th);
            }
        }

        return view('mantenimiento.reportes.ordenesreparacion.index', [
            'estaciones'     => collect($this->estacion_service->consultar(estatus: true))->pluck('estacion', 'id_estacion'),
        ]);
    }
}
