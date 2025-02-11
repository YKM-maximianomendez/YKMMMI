<?php

namespace App\Http\Controllers\Mantenimiento\KioscoReparaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TerminarActividadReparacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Tecnico Reparador']);
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden_falla, int $id_actividad_reparacion)
    {
        try {
            $id = (int) Auth::id();

            $tsql = <<< SQL
            EXECUTE dbo.usp_mtto_terminar_OT_falla_actreparacion
                    @id_actividad_reparacion = :id_actividad_reparacion,
                    @id_tecnico_reparador = :id_tecnico_reparador,
                    @tiempo_hh_add = :tiempo_hh_add
            SQL;

            $statement = DB::getPdo()->prepare($tsql);
            $statement->execute(array(
                ':id_actividad_reparacion'  => $id_actividad_reparacion,
                ':id_tecnico_reparador'     => $id,
                ':tiempo_hh_add'            => $request->float('tiempo_hh')
            ));

            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
