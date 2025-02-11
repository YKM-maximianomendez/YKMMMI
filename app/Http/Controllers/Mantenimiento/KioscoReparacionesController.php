<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\ActividadReparacionService;
use App\Services\TabuladorTmpService;
use Illuminate\Http\Request;

class KioscoReparacionesController extends Controller
{
    public function __construct(
        private readonly ActividadReparacionService $actividad_reparacion_service,
        private readonly TabuladorTmpService $tabulador_tmp_service,
    ) {
        $this->middleware(['auth', 'role:Tecnico Reparador']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('mantenimiento.kiosco-reparaciones.index', [
            'actividades_reparacion' => $this->actividad_reparacion_service->consultar(estatus: true),
            'fracciones'             => collect($this->tabulador_tmp_service->consultar())->pluck('minutos', 'fracc')
        ]);
    }
}
