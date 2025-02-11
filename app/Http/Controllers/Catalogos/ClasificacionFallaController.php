<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\ClasificacionFallaRequest;
use App\Services\Catalogos\ClasificacionFallaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClasificacionFallaController extends Controller
{
    public function __construct(private readonly ClasificacionFallaService $clasificacion_falla_service)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $resulset = $this->clasificacion_falla_service->consultar();

            return datatables($resulset)
                ->toJson();
        }

        return view('catalogos.clasificaciones-falla.index');
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
    public function store(ClasificacionFallaRequest $request)
    {
        try {
            DB::table('mtto_clasificacion_falla')->insert([
                'descripcion'           => $request->str('descripcion')->upper(),
                'id_usuario_registro'   => Auth::id(),
                'fecha_registro'        => now()->toDateTimeString(),
            ]);

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
    public function update(ClasificacionFallaRequest $request, int $id)
    {
        try {
            DB::table('mtto_clasificacion_falla')->where('id_clasificacion', $id)->update([
                'descripcion'            => $request->str('descripcion')->upper(),
                'id_usuario_modifico'    => Auth::id(),
                'fecha_modifico'         => now()->toDateTimeString(),
            ]);

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
            DB::table('mtto_clasificacion_falla')
                ->where('id_clasificacion', $id)
                ->update(array(
                    'estatus'               => 0,
                    'id_usuario_modifico'   => Auth::id(),
                    'fecha_modifico'        => now()->toDateTimeString()
                ));

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
