<?php

namespace App\Http\Requests\Mantenimiento\OrdenReparacionFalla;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProgramarFallaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_prioridad'              => ['required', 'integer', Rule::exists('mtto_prioridad_falla', 'id_prioridad')],
            'horas'                     => ['required', 'integer', 'min:0'],
            'fraccion'                  => ['required', 'numeric', Rule::exists('general_tabulador_tmp', 'fracc')],
            'id_tecnico_responsable'    => ['required', 'integer', Rule::exists('usuarios', 'id')],
        ];
    }
}
