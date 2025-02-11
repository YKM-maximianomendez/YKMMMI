<?php

namespace App\Http\Controllers\Mantenimiento\KioscoReparaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumenReparacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Tecnico Reparador']);
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id_orden_falla)
    {
        $tipo_filtro = $request->input('tipo');
        $tsql = "";

        switch ($tipo_filtro) {
            case '1':
                $tsql = "SELECT rep.id_orden, rep.id_orden_falla, rep.id_actividad_reparacion, rep.id_act_reparacion, rep.codigo, rep.actividad_reparacion, rep.tiempo_hh, rep.fecha, FORMAT(rep.fecha_captura, 'yyyy-MM-dd HH:mm') AS fecha_captura, rep.fecha_termina, rep.terminada, rep.estatus, rep.observaciones, rep.id_tecnico_reparador, rep.tecnico_reparador, rep.id_tecnico_reparador_termina, rep.tecnico_reparador_termina, rep.id_turno_captura, rep.turno_captura
                FROM dbo.v_mtto_ordenes_fallas_acts_reparacion AS rep
                WHERE(id_orden_falla=?)";
                break;
            case '2':
                $tsql = "SELECT ev.id_orden_evidencia, ev.id_orden, ev.no_orden, ev.tipo, ev.tipo_evidencia, ev.nombre, ev.mime, ev.url, FORMAT(ev.fecha_captura, 'yyyy-MM-dd HH:mm') AS fecha_captura, ev.estatus, ev.id_usuario_captura, ev.usuario_captura, ev.id_turno_captura, ev.turno_captura
                FROM dbo.v_mtto_ordenes_evidencias AS ev
                WHERE id_orden=(SELECT id_orden FROM mtto_orden_falla WHERE id_orden_falla=?)
                AND ev.tipo = 'R'";
                break;
            case '3':
                $tsql = "SELECT mu.id_orden_materialutilizado, mu.id_orden, mu.insumo_refaccion, mu.cantidad, mu.contabilizado, mu.fecha_contabilizado, mu.unidad_medida, FORMAT(mu.fecha_captura, 'yyyy-MM-dd HH:mm') AS fecha_captura, mu.id_usuario_captura, mu.usuario_captura, mu.id_turno_captura, mu.turno_captura
                FROM dbo.v_mtto_ordenes_materialutilizado AS mu
                WHERE(mu.id_orden=(SELECT id_orden FROM dbo.mtto_orden_falla WHERE id_orden_falla=?))";
                break;
            default:
                # code...
                break;
        }

        $resultset = DB::select($tsql, array($id_orden_falla));

        return datatables($resultset)
            ->toJson();
    }
}
