<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\FallaRequest;
use App\Services\Catalogos\ClasificacionFallaService;
use App\Services\Catalogos\FallaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FallaController extends Controller
{
    public function __construct(
        private readonly FallaService              $falla_service,
        private readonly ClasificacionFallaService $clasificacion_falla_service
    ) {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $resultset = $this->falla_service->consultar();

            return datatables($resultset)
                ->toJson();
        }

        $clasificaciones_falla = $this->clasificacion_falla_service->consultar();

        return view('catalogos.fallas.index', [
            'clasificaciones_falla' => collect($clasificaciones_falla)->pluck('descripcion', 'id_clasificacion'),
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
    public function store(FallaRequest $request)
    {
        try {
            DB::table('mtto_falla')->insert(array(
                'codigo'                => $this->falla_service->consecutivo(),
                'descripcion'           => $request->str('descripcion')->upper(),
                'id_clasificacion'      => $request->integer('id_clasificacion'),
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
    public function update(FallaRequest $request, int $id)
    {
        try {
            DB::table('mtto_falla')->where('id_falla', $id)->update(array(
                'descripcion'           => $request->str('descripcion')->upper(),
                'id_clasificacion'      => $request->integer('id_clasificacion'),
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
            DB::table('mtto_falla')->where('id_falla', $id)->update(array(
                'estatus'               => 0,
                'fecha_modifico'        => now()->toDateTimeString(),
                'id_usuario_modifico'   => Auth::id()
            ));

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
