<?php

namespace App\Services\Http;

use App\Services\EvidenciaFileSystemService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MantenimientoAPI
{
    public static function DownloadFORMA(int $id_orden)
    {
        try {
            $headers = [
                'accept'        => 'application/octet-stream',
                'authorization' => config('mantenimiento_api.api_key')
            ];

            $response = Http::withHeaders($headers)
                ->get(config('mantenimiento_api.url') . "FORMA/Export/{$id_orden}");

            if ($response->successful()) {
                $file_system = new EvidenciaFileSystemService();

                $file_system->store($id_orden, array(
                    'nombre_archivo' => 'FORMA.pdf',
                    'contenido'      => $response->body()
                ));

                DB::update("UPDATE mtto_orden SET [FORMA] = 1, [FORMA_created_at] = GETDATE() WHERE (id_orden = ?)", array($id_orden));

                // Log::info("FORMA {$id_orden} descargada exitosamente.");
            } else {
                // Log::error("Error al descargar el FORMA {$id_orden}: {$response->status()} - {$response->body()}");
            }
        } catch (Throwable $th) {
            // Log::error("Error al descargar el FORMA {$id_orden}: {$th->getMessage()}");
        }
    }
}
