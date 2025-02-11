<?php

namespace App\Http\Requests\Mantenimiento;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrdenReparacionRequest extends FormRequest
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
            'id_numeroparte'        => ['required', 'integer', Rule::exists('manufactura_numeroparte', 'id_numeroparte')->where('estatus', 1)],
            'id_estacion'           => ['required', 'integer', Rule::exists('manufactura_estacion', 'id_estacion')->where('estatus', 1)],
            'id_falla'              => ['required', 'integer', Rule::exists('mtto_falla', 'id_falla')->where('estatus', 1)],
            'id_causa'              => ['required', 'integer', Rule::exists('mtto_causa_falla', 'id_causa')->where('estatus', 1)],
            'operacion'             => ['required', 'string', 'regex:/(\d+)\/(\d+)/'],
            'fecha_requiere_prod'   => ['required', 'date'],
            'piezas_requeridas'     => ['nullable', 'numeric', 'min: 0'],
            'piezas_terminadas'     => ['nullable', 'numeric', 'min: 0'],
            'id_tipoatencion'       => ['required']
        ];
    }
}
