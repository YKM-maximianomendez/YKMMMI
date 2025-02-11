<?php

namespace App\Http\Controllers\Mantenimiento\OrdenReparacion;

use App\Actions\Mantenimiento\OrdenReparacion\CerrarOrdenReparacionAction;
use App\Http\Controllers\Controller;
use App\Services\EvidenciaFileSystemService;
use App\Services\Http\MantenimientoAPI;
use Illuminate\Http\Request;
use Throwable;

class CerrarOrdenReparacionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden, CerrarOrdenReparacionAction $action)
    {
        try {
            $fecha_ini = $request->input('fecha_inicio_rep');
            $fecha_fin = $request->input('fecha_fin_rep');

            $action->execute(
                id_orden: $id_orden,
                data: array(
                    'fecha_inicia_reparacion'   => now()->parse($fecha_ini)->toDateTimeString(),
                    'fecha_fin_reparacion'      => now()->parse($fecha_fin)->toDateTimeString()
                ),
                evidencias: []
            );

            // Llamada la API para descarga del documento
            try {
                MantenimientoAPI::DownloadFORMA($id_orden);
            } catch (Throwable $th) {

            }

            $file_path = EvidenciaFileSystemService::get_path($id_orden, "FORMA.pdf");

            $content = null;
            $mime_type = null;

            if (file_exists($file_path)) {
                $content = base64_encode(file_get_contents($file_path));
                $mime_type = mime_content_type($file_path);
            }

            return response()->json([
                'file' => array(
                    'content'   => $content,
                    'name'      => "FORMA-" . $id_orden . '.pdf',
                    'mime_type' => $mime_type
                )
            ]);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
