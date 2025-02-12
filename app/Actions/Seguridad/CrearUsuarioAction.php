<?php

namespace App\Actions\Seguridad;

use App\Models\User;
use App\Services\Seguridad\RolService;
use App\Services\Seguridad\UsuarioService;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CrearUsuarioAction
{
    public function __construct(private readonly RolService $rol_service) {}

    public function execute(array $data): User
    {
        $id = User::query()->insertGetId(array(
            'nombre'                => $data['nombre'],
            'correo_electronico'    => $data['correo_electronico'],
            'numero_nomina'         => $data['numero_nomina'],
            'tripulacion'           => $data['tripulacion'],
            'password'              => Hash::make(UsuarioService::DEFAULT_PASSWORD)
        ));

        $usuario = User::findOrFail($id);

        $this->rol_service->revokeRoles($usuario->id);
        $this->rol_service->syncRole($usuario, Role::findById($data['id_rol'])->name);

        return $usuario;
    }
}
