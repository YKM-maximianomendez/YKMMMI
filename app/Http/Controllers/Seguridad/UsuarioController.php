<?php

namespace App\Http\Controllers\Seguridad;

use App\Actions\Seguridad\ActualizarUsuarioAction;
use App\Actions\Seguridad\CrearUsuarioAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\UsuarioRequest;
use App\Services\Seguridad\UsuarioService;
use App\Services\TripulacionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuario_service,
        private readonly TripulacionService $tripulacion_service
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
            $resultset = $this->usuario_service->consultar();

            return datatables($resultset)
                ->toJson();
        }

        return view('seguridad.usuarios.index', [
            'roles' => Role::all()->pluck('name', 'id'),
            'tripulaciones' => collect($this->tripulacion_service->consultar())->pluck('tripulacion', 'id_tripulacion')
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
    public function store(UsuarioRequest $request, CrearUsuarioAction $action)
    {
        try {
            $action->execute($request->toArray());

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
    public function update(UsuarioRequest $request, int $id, ActualizarUsuarioAction $action)
    {
        try {
            $action->execute($id, $request->toArray());

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $th) {
            return response()->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
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
