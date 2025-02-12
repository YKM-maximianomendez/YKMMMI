<?php

namespace App\Http\Controllers\Seguridad\Usuario;

use App\Actions\Seguridad\RestablecerPasswordAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RestablecerPasswordController extends Controller
{
    public function __construct() {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id, RestablecerPasswordAction $action)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $action->execute($id);
                return response()->json(null, 204);
            } catch (Throwable $th) {
                return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
