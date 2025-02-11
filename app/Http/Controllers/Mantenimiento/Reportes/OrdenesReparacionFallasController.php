<?php

namespace App\Http\Controllers\Mantenimiento\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrdenesReparacionFallasController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            try {
                $tsql = <<< SQL
                EXECUTE usp_reportes_consultar_fallas
                    @falla_fecha_captura_inicio	= ?,
                    @falla_fecha_captura_fin	= ?,
                    @falla_id_falla			    = ?,
                    @falla_id_causa		        = ?,
                    @falla_id_turno				= ?
                SQL;

                $resultset = DB::select($tsql, array(
                    request()->input('falla_fecha_captura_inicio', now()->startOfMonth()->toDateString()),
                    request()->input('falla_fecha_captura_fin', now()->endOfMonth()->toDateString()),
                    request()->input('falla_id_falla'),
                    request()->input('falla_id_causa'),
                    request()->input('falla_id_turno')
                ));

                return datatables($resultset)
                    ->toJson();
            } catch (Throwable $th) {
                dd($th);
            }
        }

        return view('mantenimiento.reportes.ordenesreparacion-fallas.index');
    }
}
