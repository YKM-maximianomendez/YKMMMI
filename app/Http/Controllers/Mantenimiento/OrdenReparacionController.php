<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Actions\Mantenimiento\OrdenReparacion\ActualizarOrdenReparacionAction;
use App\Actions\Mantenimiento\OrdenReparacion\InsertarOrdenReparacionAction;
use App\Enums\Mantenimiento\TipoEvidenciaFalla;
use App\Enums\Mantenimiento\TipoFalla;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\OrdenReparacionRequest;
use App\Services\Catalogos\CausaFallaService;
use App\Services\Catalogos\EstacionService;
use App\Services\Catalogos\FallaService;
use App\Services\Catalogos\NumeroParteService;
use App\Services\Mantenimiento\OrdenReparacionService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrdenReparacionController extends Controller
{
    public function __construct(
        private readonly NumeroParteService     $numeroparte_service,
        private readonly EstacionService        $estacion_service,
        private readonly FallaService           $falla_service,
        private readonly CausaFallaService      $causa_falla_service,
        private readonly OrdenReparacionService $orden_reparacion_service
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

            $id_estatus_orden = request('id_estatus_orden', 1);

            $resultset = $this->orden_reparacion_service->consultar_por_estatus($id_estatus_orden, $id_usuario);

            return datatables($resultset)
                ->with(['contador' => $this->orden_reparacion_service->contador($id_usuario)])
                ->editColumn('orden_fecha_emision', fn($row) => now()->parse($row->orden_fecha_emision)->format('Y-m-d H:i'))
                ->addColumn('acciones', function($row) {
                    $orden_id_estatus = intval($row->orden_id_estatus);
                    $fallas_abiertas  = intval($row->fallas_abiertas);

                    if (request()->user()->hasRole(Roles::LIDER_TOOLROOM->value)) {
                        if ($orden_id_estatus === 1)
                            if ($fallas_abiertas === 0)
                                return <<< HTML
                                    <div>
                                        <button type="button" style="width: 55px;" class="btn btn-sm btn-warning cerrar-OT">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                            </svg>
                                        </button> 
                                    </div>
                                HTML;
                            else
                                return <<< HTML
                                    <button type="button" class="btn btn-link text-decoration-none btn-sm">
                                        <i class="bi bi-reception-3"></i> Reparando...
                                    </button>
                                HTML;
                        
                        if ($orden_id_estatus === 2)
                            return <<< HTML
                                <button type="button" class="btn btn-link text-decoration-none btn-sm">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                            HTML;
                        
                        if ($orden_id_estatus === 3)
                            return '<span class="text-success">OK</span>';
                    }

                    if (request()->user()->hasRole(Roles::LIDER_PRENSAS->value)) {
                        if ($orden_id_estatus === 1)
                            return '';

                        if ($orden_id_estatus === 2)
                            return <<< HTML
                                <button id="autorizar-btn" class="btn btn-danger confirmar-cierre-OT">
                                    <i class="fas fa-check-circle"></i> Confirmar
                                </button>
                            HTML;
                    }
                })
                ->escapeColumns('acciomes')
                ->toJson();
        }

        return view('mantenimiento.ordenesreparacion.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mantenimiento.ordenesreparacion.create', [
            'estaciones'     => collect($this->estacion_service->consultar(estatus: true))->pluck('estacion', 'id_estacion'),
            'numerosdeparte' => collect($this->numeroparte_service->consultar(estatus: true))->pluck('numeroparte', 'id_numeroparte'),
            'fallas'         => collect($this->falla_service->consultar(estatus: true))->select('codigo', 'id_falla', 'falla'),
            'causas_fallas'  => collect($this->causa_falla_service->consultar(estatus: true))->select('id_causa', 'codigo', 'causa')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrdenReparacionRequest $request, InsertarOrdenReparacionAction $action)
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

            // 1. Insertar la orden de reparación...
            [$id_orden, $id_orden_falla] = $action->execute(data: array(
                'orden' => [
                    'id_numeroparte'        => $request->integer('id_numeroparte'),
                    'id_estacion'           => $request->integer('id_estacion'),
                    'id_tipoatencion'       => $request->integer('id_tipoatencion'),
                    'id_tipomantenimiento'  => 1,
                    'piezas_terminadas'     => $request->float('piezas_terminadas'),
                    'piezas_requeridas'     => $request->float('piezas_requeridas'),
                    'operacion'             => $request->input('operacion'),
                    'id_usuario_registro'   => $id_usuario,
                    'fecha_requiere_prod'   => $request->input('fecha_requiere_prod')
                ],
                'orden_falla' => [
                    'id_falla'              => $request->integer('id_falla'),
                    'tipo'                  => TipoFalla::UNICA->value,
                    'id_usuario_registro'   => $id_usuario,
                    'id_causa'              => $request->integer('id_causa'),
                    'observaciones'         => $request->input('observaciones')
                ],
                'evidencias' => $evidencias
            ));

            // Enviar correo

            return response()->json(['message' => 'Orden registrada exitosamente.'], Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $orden = $this->orden_reparacion_service->consultar_por_id($id);
        $param = array($id);

        $orden_evidencias = DB::select("SELECT ev.id_orden_evidencia, ev.id_orden, ev.no_orden, ev.tipo, ev.tipo_evidencia, ev.nombre, ev.mime, ev.url, FORMAT(ev.fecha_captura, 'yyyy-MM-dd HH:mm') AS fecha_captura, ev.estatus, ev.id_usuario_captura, ev.usuario_captura, ev.id_turno_captura, ev.turno_captura
                FROM dbo.v_mtto_ordenes_evidencias AS ev
                WHERE (id_orden = ?)", $param);
        $orden_materialutilizado = DB::select("SELECT mu.id_orden_materialutilizado, mu.id_orden, mu.insumo_refaccion, mu.cantidad, mu.contabilizado, mu.fecha_contabilizado, mu.unidad_medida, FORMAT(mu.fecha_captura, 'yyyy-MM-dd HH:mm') AS fecha_captura, mu.id_usuario_captura, mu.usuario_captura, mu.id_turno_captura, mu.turno_captura
                FROM dbo.v_mtto_ordenes_materialutilizado AS mu
                WHERE(mu.id_orden=?)", $param);

        $orden_fallas = DB::select("SELECT mf.id_orden, mf.no_orden, mf.id_orden_falla, mf.no_falla, mf.orden_f_inicio_reparacion, mf.orden_f_fin_reparacion, mf.falla_id_causa, mf.falla_codigo_causa, mf.falla_causa, mf.falla_id_falla, mf.falla_codigo_falla, mf.falla_falla, mf.falla_id_estatus, mf.falla_estatus, mf.falla_id_prioridad, mf.falla_prioridad, mf.falla_prioridad_nivel, mf.falla_id_turno, mf.falla_turno, mf.falla_fecha_captura, mf.falla_fecha_recepcion, mf.falla_fecha_programacion, mf.falla_fecha_termino, mf.falla_tiempo_hh_estimado, mf.falla_tiempo_hh_real, mf.falla_observaciones, mf.falla_id_usuario_registro, mf.falla_usuario_registro, mf.falla_id_usuario_recibe, mf.falla_usuario_recibe, mf.falla_id_usuario_programa, mf.falla_usuario_programa, mf.falla_id_usuario_termina, mf.falla_usuario_termina, mf.falla_id_usuario_responsable, mf.falla_usuario_responsable, mf.falla_id_tecnico_responsable, mf.falla_tecnico_responsable, mf.falla_tipo, mf.falla_num_reparaciones, mf.falla_num_reparaciones_pendientes, mf.falla_tiempo_hh_real_total
            FROM v_mtto_ordenes_fallas AS mf
            WHERE (mf.id_orden = ?)", $param);

        return response()->json([
            'orden' => $orden,
            'orden_fallas' => $orden_fallas,
            'orden_evidencias' => $orden_evidencias,
            'orden_material_utilizado' => $orden_materialutilizado
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $tsql = "SELECT mof.no_orden, mof.id_orden_falla, mof.id_orden, mof.falla_id_falla, mof.orden_operacion, mof.orden_pzas_requeridas, mof.orden_pzas_terminadas, mof.falla_id_causa, mof.falla_observaciones, mof.orden_id_numeroparte, mof.orden_id_estacion, mof.orden_fecha_requiere_prod, mof.orden_id_tipoatencion
        FROM dbo.v_mtto_ordenes_fallas AS mof
        WHERE(mof.id_orden=?) AND (mof.falla_tipo='U')";

        $result = DB::selectOne($tsql, array($id));

        if (empty($result)) {
            return response()->json(['message' => 'Orden de reparación no encontrada.'], 404);
        }

        return response()->json([
            'orden' => $result
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id, ActualizarOrdenReparacionAction $action)
    {
        try {
            $action->execute(
                id_orden: $id,
                data: array(
                    'orden' => array(
                        'id_numeroparte'      => $request->integer('id_numeroparte'),
                        'id_estacion'         => $request->integer('id_estacion'),
                        'id_tipoatencion'     => $request->integer('id_tipoatencion'),
                        'operacion'           => $request->input('operacion'),
                        'pzas_requeridas'     => $request->input('piezas_requeridas'),
                        'pzas_terminadas'     => $request->input('piezas_terminadas'),
                        'fecha_requiere_prod' => $request->input('fecha_requiere_prod')
                    ),
                    'orden_falla' => array(
                        'id_falla'            => $request->integer('id_falla'),
                        'id_causa'            => $request->integer('id_causa'),
                        'observaciones'       => $request->input('observaciones'),
                    )
                )
            );

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
    }
}
