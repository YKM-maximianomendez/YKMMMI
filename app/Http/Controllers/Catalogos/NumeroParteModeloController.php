<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\NumeroParteModeloRequest;
use App\Services\Catalogos\NumeroParteModeloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NumeroParteModeloController extends Controller
{
    public function __construct(private readonly NumeroParteModeloService $numeroparte_modelo_service)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $resultset = $this->numeroparte_modelo_service->consultar();

            return datatables($resultset)
                ->toJson();
        }

        return view('catalogos.numerosdeparte-modelo.index');
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
    public function store(NumeroParteModeloRequest $request)
    {
        try {
            DB::table('manufactura_modelo')->insert(array(
                'modelo'                => $request->input('modelo'),
                'descripcion'           => $request->str('descripcion')->upper(),
                'id_usuario_registro'   => Auth::id(),
                'fecha_registro'        => now()->format('Y-m-d H:i:s'),
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
    public function update(NumeroParteModeloRequest $request, int $id)
    {
        try {
            DB::table('manufactura_modelo')->where('id_modelo', $id)->update(array(
                'modelo'                => $request->input('modelo'),
                'descripcion'           => $request->str('descripcion')->upper(),
                'id_usuario_modifico'   => Auth::id(),
                'fecha_modifico'        => now()->format('Y-m-d H:i:s')
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
            DB::table('manufactura_modelo')->where('id_modelo', $id)
                ->update(array(
                    'estatus'               => 0,
                    'id_usuario_modifico'   => Auth::id(),
                    'fecha_modifico'        => now()->format('Y-m-d H:i:s')
                ));

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
