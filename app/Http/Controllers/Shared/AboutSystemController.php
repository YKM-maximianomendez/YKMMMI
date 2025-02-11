<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Services\ParametroService;
use Illuminate\Http\Request;

class AboutSystemController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ParametroService $service)
    {
        return response()->json([
            'information' => [
                'system_name'       => config('app.name'),
                'version'           => $service->obtener_valor('sys_version', 'SISTEMA'),
                'version_date'      => $service->obtener_valor('sys_version_last_update', 'SISTEMA'),
                'user_manual_link'  => ''
            ]
        ]);
    }
}
