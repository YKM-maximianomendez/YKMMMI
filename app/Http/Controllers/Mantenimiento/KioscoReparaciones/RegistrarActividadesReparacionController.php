<?php

namespace App\Http\Controllers\Mantenimiento\KioscoReparaciones;

use App\Actions\Mantenimiento\OrdenReparacionFalla\RegistrarActividadesReparacionAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RegistrarActividadesReparacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Tecnico Reparador']);
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, RegistrarActividadesReparacionAction $action)
    {
        try {
            $id = (int) Auth::id();

            $input_actividades = $request->input('actividades');
            
            if (count($input_actividades) === 0) {
                return response()->json(['message' => "Ninguna actividad reportada."], 409);
            }

            $actividades = array_map(function (array $actividad) use ($id) {
                return array(
                    'id_tecnico_reparador'  => $id,
                    'id_act_reparacion'     => $actividad['id_act_reparacion'],
                    'fecha'                 => $actividad['fecha'],
                    'tiempo_hh'             => $actividad['tiempo_hh'],
                    'observaciones'         => $actividad['observaciones'],
                    'terminada'             => $actividad['terminada'],
                );
            }, $input_actividades);

            $action->execute(id_orden_falla: $request->integer('id_orden_falla'), actividades_reparacion: $actividades);

            return response()->json(null, Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
