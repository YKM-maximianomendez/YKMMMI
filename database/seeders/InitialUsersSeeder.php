<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InitialUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = storage_path('app') . DIRECTORY_SEPARATOR . 'usuarios.csv';
        
        $usuarios = [];

        if (($handle = fopen($csvFile, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                array_push($usuarios, array(
                    'numero_nomina'      => $data[0],
                    'nombre'             => mb_convert_encoding($data[1], 'UTF-8', 'auto'),
                    'correo_electronico' => empty($data[2]) ? null : $data[2],
                    'tripulacion'        => empty($data[3]) ? null : $data[3],
                    'rol'                => $data[4]
                ));
            }
            fclose($handle);
        } else {
            echo "Error al abrir el archivo CSV.";
        }

        if (count($usuarios) > 0) {
            array_shift($usuarios);

            foreach ($usuarios as $usuario) {
                $numero_nomina = $usuario['numero_nomina'];
                
                if (!User::query()->where('numero_nomina', $numero_nomina)->exists()) {

                    $user = User::create([
                        'nombre'                => $usuario['nombre'],
                        'correo_electronico'    => $usuario['correo_electronico'],
                        'numero_nomina'         => $numero_nomina,
                        'tripulacion'           => $usuario['tripulacion'],
                        'password'              => Hash::make('password') // fixed
                    ]);

                    $role = $usuario['rol'];

                    if (Role::query()->where('name', $role)->exists()) {
                        $user->assignRole(Roles::tryFrom($role)->value);
                    }
                }
            }
        }
    }
}
