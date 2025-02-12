<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Actions\Mantenimiento\OrdenReparacionFalla\InsertarOrdenReparacionFallaAction;
use App\Enums\Mantenimiento\TipoEvidenciaFalla;
use App\Enums\Mantenimiento\TipoFalla;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\OrdenReparacionFallaRequest;
use App\Services\Catalogos\CausaFallaService;
use App\Services\Catalogos\FallaService;
use App\Services\Mantenimiento\OrdenReparacionFallaService;
use App\Services\PrioridadService;
use App\Services\TabuladorTmpService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrdenReparacionFallaController extends Controller
{
    public function __construct(
        private readonly PrioridadService $prioridad_service,
        private readonly TabuladorTmpService $tabulador_tmp_service,
        private readonly OrdenReparacionFallaService $orden_reparacion_falla_service,
        private readonly FallaService           $falla_service,
        private readonly CausaFallaService      $causa_falla_service,
    ) {
        $this->middleware('auth');
        $this->middleware(['role:Lider Prensas|Lider ToolRoom']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $id_usuario = request()->user()->hasRole(Roles::LIDER_PRENSAS->value)
                ? Auth::id()
                : null;

            $resultset = $this->orden_reparacion_falla_service->consultar_por_estatus(
                request()->input('estatus_orden_falla'),
                $id_usuario
            );

            $roles_check = array(
                'es_lider_prensas'       => request()->user()->hasRole(Roles::LIDER_PRENSAS->value),
                'es_lider_mantenimiento' => request()->user()->hasRole(Roles::LIDER_TOOLROOM->value)
            );

            return datatables($resultset)
                ->with(['contador' => $this->orden_reparacion_falla_service->contador($id_usuario)])
                ->editColumn('orden_fecha_emision', function ($row) {
                    return now()->parse($row->orden_fecha_emision)->format('Y-m-d H:i');
                })
                ->editColumn('falla_fecha_programacion', function ($row) {
                    $fecha = $row->falla_fecha_programacion;
                    return is_null($fecha) ? null : now()->parse($fecha)->format('Y-m-d H:i');
                })
                ->editColumn('falla_fecha_termino', function ($row) {
                    $fecha = $row->falla_fecha_termino;
                    return is_null($fecha) ? null : now()->parse($fecha)->format('Y-m-d H:i');
                })
                ->addColumn('acciones', function ($row) use ($roles_check) {
                    $falla_id_estatus = intval($row->falla_id_estatus);
                    $orden_id_estatus = intval($row->orden_id_estatus);
                    $reparaciones     = intval($row->falla_num_reparaciones);
                    $id_orden         = $row->id_orden;
                    $no_falla         = $row->no_falla;
                    $id_orden_falla   = $row->id_orden_falla;

                    if ($roles_check['es_lider_mantenimiento']) {
                        if ($falla_id_estatus === 1) {
                            return <<< HTML
                                <button type="button" style="width: 55px;" class="btn btn-sm fw-bolder btn-primary programar-OT">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch-fill" viewBox="0 0 16 16">
                                        <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07A7.001 7.001 0 0 0 8 16a7 7 0 0 0 5.29-11.584l.013-.012.354-.354.353.354a.5.5 0 1 0 .707-.707l-1.414-1.415a.5.5 0 1 0-.707.707l.354.354-.354.354-.012.012A6.97 6.97 0 0 0 9 2.071V1h.5a.5.5 0 0 0 0-1zm2 5.6V9a.5.5 0 0 1-.5.5H4.5a.5.5 0 0 1 0-1h3V5.6a.5.5 0 1 1 1 0"/>
                                    </svg>
                                </button>
                            HTML;
                        } 
                        
                        if ($falla_id_estatus === 3) {
                            if ($reparaciones === 0) {
                                return <<< HTML
                                    <button type="button" style="width: 55px;" class="btn btn-sm btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                                            <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                                        </svg>
                                    </button>
                                HTML;
                            }

                            return <<< HTML
                                <button type="button" style="width: 55px;" class="btn fw-bold btn-sm btn-warning cerrar-falla" data-orden="{$id_orden}" data-num-falla="{$no_falla}" data-falla="{$id_orden_falla}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </button>
                            HTML;
                        } 
                        
                        if ($falla_id_estatus === 4 && $orden_id_estatus === 1) {
                            return <<< HTML
                                <button type="button" style="width: 55px;" class="btn btn-sm btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                                    </svg>
                                </button>
                            HTML;
                        } else {
                            return '';
                        }

                        return '';
                    }
                    
                    return '';
                })
                ->escapeColumns('acciones')
                ->toJson();
        }

        return view('mantenimiento.ordenesreparacion-falla.index', [
            'prioridades'           => collect($this->prioridad_service->consultar())->pluck('prioridad', 'id_prioridad'),
            'tecnicos_reparadores'  => collect(DB::select("SELECT id, nombre FROM v_usuarios WHERE rol = ? AND estatus = 1", array(Roles::TECNICO_REPARADOR->value)))->pluck('nombre', 'id'),
            'fracciones'            => collect($this->tabulador_tmp_service->consultar())->pluck('minutos', 'fracc'),
            'rol'                   => request()->user()->roles[0],
            'fallas'                => collect($this->falla_service->consultar(estatus: true))->select(['id_falla', 'codigo', 'falla']),
            'causas_fallas'         => collect($this->causa_falla_service->consultar(estatus: true))->select('id_causa', 'codigo', 'causa')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrdenReparacionFallaRequest $request, InsertarOrdenReparacionFallaAction $action)
    {
        try {
            $id_usuario = (int) Auth::id();
            $evidencias = [];

            // 0. Preparación de las evidencias...
            if ($request->hasFile('evidencias')) {
                $archivos = $request->file('evidencias');

                $evidencias = array_map(function (UploadedFile $archivo) use ($id_usuario) {
                    return array(
                        'contenido'          => $archivo->getContent(),
                        'nombre_archivo'     => uniqid() . "." . $archivo->getClientOriginalExtension(),
                        'MIME'               => $archivo->getClientMimeType(),
                        'tipo_evidencia'     => TipoEvidenciaFalla::FALLA->value,
                        'id_usuario_captura' => $id_usuario
                    );
                }, $archivos);
            }

            $action->execute(
                id_orden: $request->integer('id_orden'),
                data: array(
                    'orden_falla' => array(
                        'id_falla'              => $request->integer('id_falla'),
                        'tipo'                  => TipoFalla::ADICIONAL->value,
                        'id_usuario_registro'   => $id_usuario,
                        'id_causa'              => $request->integer('id_causa'),
                        'observaciones'         => $request->input('observaciones')
                    ),
                    'evidencias' => $evidencias
                )
            );

            return response()->json(null, Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $orden_falla = DB::selectOne("SELECT mf.id_orden, mf.no_orden, mf.id_orden_falla, mf.no_falla, mf.falla_codigo_causa, mf.falla_causa, mf.falla_codigo_falla, mf.falla_falla, mf.falla_estatus, mf.falla_prioridad, mf.falla_prioridad_nivel, mf.falla_turno, mf.falla_fecha_captura, mf.falla_fecha_recepcion, mf.falla_fecha_programacion, mf.falla_fecha_termino, mf.falla_tiempo_hh_estimado, mf.falla_tiempo_hh_real, mf.falla_observaciones, mf.falla_usuario_registro, mf.falla_id_usuario_recibe, mf.falla_usuario_recibe, mf.falla_usuario_programa, mf.falla_usuario_termina, mf.falla_usuario_responsable, mf.falla_tecnico_responsable, mf.falla_tipo, mf.falla_num_reparaciones, mf.falla_num_reparaciones_pendientes, mf.falla_tiempo_hh_real_total
        FROM v_mtto_ordenes_fallas AS mf
        WHERE (mf.id_orden_falla = ?)", array($id));

        if (empty($orden_falla)) {
            return response()->json(['message' => "Falla no encontrada."], 404);
        }

        return response()->json([
            'falla' => $orden_falla,
            'falla_evidencias' => [],
            'falla_actividades_reparacion' => []
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
    }
}
