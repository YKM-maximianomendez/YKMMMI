<?php

namespace App\Services\Seguridad;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolService
{
    public function revokeRoles(int $id_usuario): int
    {
        return DB::table('model_has_roles')
            ->where('model_id', $id_usuario)
            ->delete();
    }

    public function syncRole(User $usuario, string $rol): void
    {
        $usuario->syncRoles($rol);
    }

    public function findRole(int $id): ?Role
    {
        return Role::findById($id);
    }
}
