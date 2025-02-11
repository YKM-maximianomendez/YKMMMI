<?php

namespace App\Http\Controllers\Catalogos;

use App\Actions\Catalogos\NumeroParte\ActualizarNumeroParteAction;
use App\Actions\Catalogos\NumeroParte\InsertarNumeroParteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\NumeroParteRequest;
use App\Services\Catalogos\EstacionService;
use App\Services\Catalogos\NumeroParteModeloService;
use App\Services\Catalogos\NumeroParteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NumeroParteController extends Controller
{
    public function __construct(
        private readonly NumeroParteService       $numero_parte_service,
        private readonly NumeroParteModeloService $modelo_service,
        private readonly EstacionService          $estacion_service
    ) 
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $resultset = $this->numero_parte_service->consultar();

            return datatables($resultset)
                ->toJson();
        }

        return view('catalogos.numerosdeparte.index', [
            'modelos'       => collect($this->modelo_service->consultar())->pluck("modelo", "id_modelo"),
            'estaciones'    => collect($this->estacion_service->consultar())->pluck('estacion', 'id_estacion')
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
    public function store(NumeroParteRequest $request, InsertarNumeroParteAction $action)
    {
        try {
            $action->execute(data: array(
                'numeroparte' => array(
                    'numeroparte'           => $request->str('numeroparte')->toString(),
                    'nombre'                => $request->str('nombre')->upper()->toString(),
                    'id_usuario_registro'   => Auth::id(),
                    'id_estacion'           => $request->integer('id_estacion')
                ),
                'modelos' => array_map(fn($modelo) => intval($modelo), $request->input('id_modelo'))
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
    public function update(NumeroParteRequest $request, int $id, ActualizarNumeroParteAction $action)
    {
        try {
            $action->execute(
                id_numeroparte: $id,
                data: array(
                'numeroparte' => array(
                    'numeroparte'           => $request->str('numeroparte')->toString(),
                    'nombre'                => $request->str('nombre')->upper()->toString(),
                    'id_usuario_modifico'   => Auth::id(),
                    'id_estacion'           => $request->integer('id_estacion')
                ),
                'modelos' => array_map(fn($modelo) => intval($modelo), $request->input('id_modelo'))
            ));

            return response()->json(null, Response::HTTP_CREATED);
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
