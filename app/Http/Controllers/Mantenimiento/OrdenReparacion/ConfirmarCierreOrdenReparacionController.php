<?php

namespace App\Http\Controllers\Mantenimiento\OrdenReparacion;

use App\Actions\Mantenimiento\OrdenReparacion\ConfirmarCierreOrdenReparacionAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class ConfirmarCierreOrdenReparacionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden, ConfirmarCierreOrdenReparacionAction $action)
    {
        try {
            $action->execute(
                id_orden: $id_orden,
                data: array(
                    'id_usuario_prensas' => $request->user()->id
                )
            );
            
            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
