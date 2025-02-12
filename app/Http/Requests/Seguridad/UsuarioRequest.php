<?php

namespace App\Http\Requests\Seguridad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends FormRequest
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
        $rules = [
            'nombre'                => ['required', 'string'],
            'correo_electronico'    => ['nullable', 'email'],
            'tripulacion'           => ['nullable'],
            'id_rol'                => ['required'],
            'numero_nomina'         => ['required', 'numeric'],
        ];

        $id_usuario = $this->route('usuario');

        if ($id_usuario) {
            $rules['numero_nomina'][] = Rule::unique('usuarios', 'numero_nomina')->ignore($id_usuario, 'id');
        } else {
            $rules['numero_nomina'][] = Rule::unique('usuarios', 'numero_nomina');
        }

        return $rules;
    }
}
