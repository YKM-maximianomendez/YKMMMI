<?php

namespace App\Http\Controllers\Mantenimiento\Ordenreparacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenesAbiertasController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $resulset = DB::select("SELECT id_orden, no_orden FROM v_mtto_ordenes WHERE orden_id_estatus = 1");
        return response()->json($resulset, 200);
    }
}
