<?php

namespace App\Http\Controllers\Mantenimiento\OrdenReparacionFalla;

use App\Actions\Mantenimiento\OrdenReparacionFalla\CerrarFallaAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class CerrarFallaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden, int $id_orden_falla, CerrarFallaAction $action)
    {
        try {
            $action->execute($id_orden, $id_orden_falla);

            // Envio de notificacion por correo.

            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
