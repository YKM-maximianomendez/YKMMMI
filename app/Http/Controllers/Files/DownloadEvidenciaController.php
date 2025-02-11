<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;
use App\Services\EvidenciaFileSystemService;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DownloadEvidenciaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden_evidencia)
    {
        try {
            $evidencia = DB::selectOne("SELECT nombre, MIME, id_orden FROM dbo.mtto_orden_evidencia WHERE id_orden_evidencia = ?", [$id_orden_evidencia]);

            if (empty($evidencia)) {
                throw new Exception("Registro de evidencia no encontrado.");
            }

            $file_path = EvidenciaFileSystemService::get_path($evidencia->id_orden, $evidencia->nombre);

            if (!file_exists($file_path)) {
                throw new FileNotFoundException("Evidencia no encontrada.");
            }

            return response()->file($file_path, [
                'Content-Type'        => $evidencia->{'MIME'},
                'Content-Disposition' => 'inline; filename="' . basename($file_path) . '"'
            ]);
        } catch (Throwable $th) {
            throw $th;
        }
    }
}
