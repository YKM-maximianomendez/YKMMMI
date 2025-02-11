<?php

namespace App\Services;

class EvidenciaFileSystemService
{
    public function store(int $id_orden, array $evidencia): bool
    {
        $directory_path = config('filesystems.disks.mantenimiento.root');

        if (! is_dir($directory_path)) {
            @mkdir($directory_path, 0777, true);
        }

        $folder_path = $directory_path .  DIRECTORY_SEPARATOR . $id_orden;

        if (! is_dir($folder_path)) {
            @mkdir($folder_path, 0777, true);
        }

        $path = $folder_path . DIRECTORY_SEPARATOR . $evidencia['nombre_archivo'];

        return @file_put_contents($path, $evidencia['contenido']);
    }

    public static function get_path(int $id_orden, string $nombre): string
    {
        return join(DIRECTORY_SEPARATOR, array(
            config('filesystems.disks.mantenimiento.root'),
            $id_orden,
            $nombre
        ));
    }
}
