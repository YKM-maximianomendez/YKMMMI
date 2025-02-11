<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EstacionRequest extends FormRequest
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
            'descripcion' => ['required', 'string'],
            'estacion'    => ['required', 'string']
        ];

        $id_estacion = $this->route('estacion');

        if ($id_estacion) {
            $rules['estacion'][] = Rule::unique('manufactura_estacion', 'estacion')->ignore($id_estacion, 'id_estacion');
        } else {
            $rules['estacion'][] = Rule::unique('manufactura_estacion', 'estacion');
        }

        return $rules;
    }
}
