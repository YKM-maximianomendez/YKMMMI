<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Actions\Mantenimiento\OrdenReparacionFalla\InsertarOrdenReparacionFallaAction;
use App\Enums\Mantenimiento\TipoEvidenciaFalla;
use App\Enums\Mantenimiento\TipoFalla;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\OrdenReparacionFallaRequest;
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
        private readonly OrdenReparacionFallaService $orden_reparacion_falla_service
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
                'es_lider_prensas'  => request()->user()->hasRole(Roles::LIDER_PRENSAS->value),
                'es_lider_toolroom' => request()->user()->hasRole(Roles::LIDER_TOOLROOM->value)
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
                ->addColumn('es_lider_prensas', fn() => $roles_check['es_lider_prensas'])
                ->addColumn('es_lider_toolroom', fn() => $roles_check['es_lider_toolroom'])
                ->toJson();
        }

        return view('mantenimiento.ordenesreparacion-falla.index', [
            'prioridades'           => collect($this->prioridad_service->consultar())->pluck('prioridad', 'id_prioridad'),
            'tecnicos_reparadores'  => collect(DB::select("SELECT id, nombre FROM v_usuarios WHERE rol = ? AND estatus = 1", array(Roles::TECNICO_REPARADOR->value)))->pluck('nombre', 'id'),
            'fracciones'            => collect($this->tabulador_tmp_service->consultar())->pluck('minutos', 'fracc'),
            'rol'                   => request()->user()->roles[0]
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
