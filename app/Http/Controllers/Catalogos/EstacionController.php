<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\EstacionRequest;
use App\Services\Catalogos\EstacionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EstacionController extends Controller
{
    public function __construct(private readonly EstacionService $estacion_service)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $resultset = $this->estacion_service->consultar();

            return datatables($resultset)
                ->toJson();
        }

        return view('catalogos.estaciones.index');
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
    public function store(EstacionRequest $request)
    {
        try {
            $inserted = DB::table('manufactura_estacion')
                ->insert(array(
                    'estacion'              => $request->str('estacion')->toString(),
                    'descripcion'           => $request->str('descripcion')->upper(),
                    'id_usuario_registro'   => Auth::id(),
                    'fecha_registro'        => now()->toDateTimeString(),
                ));

            if ($inserted == false) throw new Exception("Estación no registrada.");

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
        $resultset = $this->estacion_service->consultar_numerosdeparte(id_estacion: $id);

        return datatables($resultset)
            ->toJson();
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
    public function update(EstacionRequest $request, int $id)
    {
        try {
            $rowcount = DB::table('manufactura_estacion')
                ->where('id_estacion', $id)
                ->update(array(
                    'estacion'              => $request->str('estacion')->toString(),
                    'descripcion'           => $request->str('descripcion')->upper(),
                    'id_usuario_modifico'   => Auth::id(),
                    'fecha_modifico'        => now()->toDateTimeString(),
                ));

            if ($rowcount == 0) throw new Exception("Estación no actualizada.");

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
