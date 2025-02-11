<?php

namespace App\Actions\Seguridad;

use App\Models\User;
use App\Services\Seguridad\RolService;
use Spatie\Permission\Models\Role;

class ActualizarUsuarioAction
{
    public function __construct(private readonly RolService $rol_service) {}

    public function execute(int $id, array $data): void
    {
        $usuario = User::findOrFail($id);
        
        $usuario->update(array(
            'nombre'                => $data['nombre'],
            'correo_electronico'    => $data['correo_electronico'],
            'numero_nomina'         => $data['numero_nomina'],
            'tripulacion'           => $data['tripulacion'],
        ));

        $this->rol_service->revokeRoles($usuario->id);
        $this->rol_service->syncRole($usuario, Role::findById($data['id_rol'])->name);
    }
}
