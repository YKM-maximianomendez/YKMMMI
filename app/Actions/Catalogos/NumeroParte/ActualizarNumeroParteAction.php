<?php

namespace App\Actions\Catalogos\NumeroParte;

use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class ActualizarNumeroParteAction
{
    public function execute(int $id_numeroparte, array $data): void
    {
        try {
            $numeroparte = $data['numeroparte'];

            $rowcount = DB::table('manufactura_numeroparte')->where('id_numeroparte', $id_numeroparte)
                ->update(array(
                    'numeroparte'           => $numeroparte['numeroparte'],
                    'nombre'                => $numeroparte['nombre'],
                    'id_usuario_modifico'   => $numeroparte['id_usuario_modifico'],
                    'fecha_modifico'        => now()->toDateTimeString()
                ));

            if ($rowcount == 0) throw new Exception("Numero de parte no actualizado.");

            $rowcount = DB::table('manufactura_numeroparte_modelo')
                ->where('id_numeroparte', $id_numeroparte)
                ->delete();

            if ($rowcount == 0) throw new Exception("Numero de parte no actualizado.");

            $modelos = array_map(function ($modelo) use ($id_numeroparte) {
                return array(
                    'id_modelo'      => $modelo,
                    'id_numeroparte' => $id_numeroparte
                );
            }, $data['modelos']);

            DB::table('manufactura_numeroparte_modelo')->insert($modelos);

            $rowcount = DB::table('manufactura_estacion_numeroparte')
                ->where('id_numeroparte', $id_numeroparte)
                ->delete();

            $id_estacion = $numeroparte['id_estacion'] ?? null;
            
            if (!empty($id_estacion)) {
                DB::table('manufactura_estacion_numeroparte')->insert(array(
                    'id_numeroparte'  => $id_numeroparte,
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
