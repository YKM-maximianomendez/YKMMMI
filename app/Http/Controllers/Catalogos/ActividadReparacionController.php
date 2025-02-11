<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\ActividadReparacionRequest;
use App\Services\Catalogos\ActividadReparacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ActividadReparacionController extends Controller
{
    public function __construct(private readonly ActividadReparacionService $actividad_reparacion_service)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $resultset = $this->actividad_reparacion_service->consultar();

            return datatables($resultset)
                ->toJson();
        }

        return view('catalogos.actividades-reparacion.index');
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
    public function store(ActividadReparacionRequest $request)
    {
        try {
            DB::table('mtto_act_reparacion')
                ->insert(array(
                    'codigo'                => $this->actividad_reparacion_service->consecutivo(),
                    'descripcion'           => $request->str('descripcion')->upper(),
                    'id_usuario_registro'   => Auth::id()
                ));

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
        //
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
    public function update(ActividadReparacionRequest $request, int $id)
    {
        try {
            DB::table('mtto_act_reparacion')
                ->where('id_act_reparacion', $id)
                ->update(array(
                    'descripcion'           => strtoupper($request->str('descripcion')->upper()),
                    'id_usuario_modifico'   => Auth::id(),
                    'fecha_modifico'        => now()->toDateTimeString()
                ));

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
        try {
            DB::table('mtto_act_reparacion')
                ->where('id_act_reparacion', $id)
                ->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
