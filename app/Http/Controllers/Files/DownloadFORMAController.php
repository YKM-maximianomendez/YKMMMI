<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;
use App\Services\EvidenciaFileSystemService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Throwable;

class DownloadFORMAController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden)
    {
        try {
            $file_path = EvidenciaFileSystemService::get_path($id_orden, 'FORMA.pdf');

            if (!file_exists($file_path)) {
                throw new FileNotFoundException("Documento FORMA no encontrado.");
            }

            return response()->file($file_path, [
                'Content-Type'        => mime_content_type($file_path),
                'Content-Disposition' => 'inline; filename="' . basename($file_path) . '"'
            ]);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
