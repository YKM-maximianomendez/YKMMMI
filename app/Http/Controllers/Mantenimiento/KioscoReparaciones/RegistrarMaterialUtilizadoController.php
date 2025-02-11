<?php

namespace App\Http\Controllers\Mantenimiento\KioscoReparaciones;

use App\Actions\Mantenimiento\OrdenReparacion\RegistrarMaterialUtilizadoAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RegistrarMaterialUtilizadoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Tecnico Reparador']);
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, RegistrarMaterialUtilizadoAction $action)
    {
        try {
            $id = (int) Auth::id();

            $input_materialutilizado = $request->input('material_utilizado');
            
            if (count($input_materialutilizado) === 0) {
                return response()->json(['message' => "Ningun material utilizado reportado."], 409);
            }

            $material_utilizado = array_map(function (array $insumo) use ($id) {
                return array(
                    'insumo_refaccion'   => $insumo['refaccion'],
                    'cantidad'           => $insumo['cantidad'],
                    'id_usuario_captura' => $id
                );
            }, $input_materialutilizado);

            $action->execute(id_orden: $request->integer('id_orden'), material_utilizado: $material_utilizado);

            return response()->json(null, Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
