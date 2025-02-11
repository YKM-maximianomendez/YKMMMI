<?php

namespace App\Actions\Catalogos\NumeroParte;

use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class InsertarNumeroParteAction
{
    public function execute(array $data): void
    {
        DB::beginTransaction();

        try {
            $numeroparte = $data['numeroparte'];

            $id_numeroparte = DB::table('manufactura_numeroparte')->insertGetId(array(
                'numeroparte'           => $numeroparte['numeroparte'],
                'nombre'                => $numeroparte['nombre'],
                'id_usuario_registro'   => $numeroparte['id_usuario_registro']
            ));

            if (empty($id_numeroparte)) throw new Exception("Numero de parte no registrado.");

            $modelos = array_map(function ($modelo) use ($id_numeroparte) {
                return array(
                    'id_modelo'      => $modelo,
                    'id_numeroparte' => $id_numeroparte
                );
            }, $data['modelos']);

            DB::table('manufactura_numeroparte_modelo')->insert($modelos);

            $id_estacion = $numeroparte['id_estacion'] ?? null;

            if (!empty($id_estacion)) {
                DB::table('anufactura_estacion_numeroparte')->insert(array(
                    'id_numero_parte' => $id_numeroparte,
                    'id_estacion'     => $id_estacion
                ));
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
