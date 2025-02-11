<?php

namespace App\Http\Controllers\Mantenimiento\KioscoReparaciones;

use App\Actions\Mantenimiento\OrdenReparacion\RegistrarEvidenciasAction;
use App\Enums\Mantenimiento\TipoEvidenciaFalla;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RegistrarEvidenciasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Tecnico Reparador']);
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, RegistrarEvidenciasAction $action)
    {
        try {
            $id = (int) Auth::id();
            $evidencias = [];

            if (!$request->has('evidencias')) {
                return response()->json(['message' => "Ninguna evidencia reportada."], 409);
            }

            $input_evidencias = $request->file('evidencias');

            $evidencias = array_map(function (UploadedFile $archivo) use ($id) {
                return array(
                    'tipo_evidencia'        => TipoEvidenciaFalla::REPARACION->value,
                    'MIME'                  => $archivo->getMimeType(),
                    'nombre_archivo'        => uniqid() . "." . $archivo->getClientOriginalExtension(),
                    'id_usuario_captura'    => $id,
                    'contenido'             => $archivo->getContent()
                );
            }, $input_evidencias);

            $id_orden = $request->integer('id_orden');

            if (DB::table('mtto_orden_evidencia')->where('id_orden', $id_orden)->count() == 3) {
                return response()->json(['message' => "No es posible reportar mas de 3 evidencias."], 409);
            }

            $action->execute($id_orden, $evidencias);

            return response()->json(null, Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
