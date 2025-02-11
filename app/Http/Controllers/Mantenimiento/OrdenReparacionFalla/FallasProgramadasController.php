<?php

namespace App\Http\Controllers\Mantenimiento\OrdenReparacionFalla;

use App\Http\Controllers\Controller;
use App\Services\Mantenimiento\OrdenReparacionFallaService;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class FallasProgramadasController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, OrdenReparacionFallaService $orden_reparacion_falla_service)
    {
        try {
            $resultset = $orden_reparacion_falla_service->consultar_fallas_programadas();
            
            return response()->json($resultset);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
