<?php

namespace App\Http\Controllers\Mantenimiento\OrdenReparacionFalla;

use App\Actions\Mantenimiento\OrdenReparacionFalla\ProgramarFallaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\OrdenReparacionFalla\ProgramarFallaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ProgramarFallaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ProgramarFallaRequest $request, int $id_orden, int $id_orden_falla, ProgramarFallaAction $action)
    {
        try {
            $tiempo_hh_estimado = (int) $request->input('horas') + (float) $request->input('fraccion');

            $action->execute(
                data: array(
                    'id_orden'               => $id_orden,
                    'id_orden_falla'         => $id_orden_falla,
                    'tiempo_hh_estimado'     => sprintf("%.2f", $tiempo_hh_estimado),
                    'id_usuario_programa'    => Auth::id(),
                    'id_tecnico_responsable' => $request->integer('id_tecnico_responsable', 1),
                    'id_prioridad'           => $request->integer('id_prioridad')
                )
            );

            // Envio de notificacion por correo.

            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
