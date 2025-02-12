<?php

namespace App\Actions\Seguridad;

use App\Models\User;
use App\Services\Seguridad\UsuarioService;
use Illuminate\Support\Facades\Hash;

class RestablecerPasswordAction
{
    public function execute(int $id): void
    {
        $user = User::findOrFail($id);

        $user->password = Hash::make(UsuarioService::DEFAULT_PASSWORD);
        $user->password_change_required = false;
        $user->password_change_datetime = now()->toDateTimeString();
        $user->saveOrFail();
    }
}
