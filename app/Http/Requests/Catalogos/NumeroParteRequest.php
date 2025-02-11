<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class NumeroParteRequest extends FormRequest
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
            'numeroparte'        => ['required', 'string'],
            'nombre'             => ['required', 'string'],
            'id_modelo'          => ['required', 'array'],
            'id_estacion'        => ['sometimes', 'integer']
        ];

        $id_numeroparte = $this->route('numeroparte');

        if ($id_numeroparte) {
            $rules['numeroparte'][] = Rule::unique('manufactura_numeroparte', 'numeroparte')->ignore($id_numeroparte, 'id_numeroparte');
        } else {
            $rules['numeroparte'][] = Rule::unique('manufactura_numeroparte', 'numeroparte');
        }

        return $rules;
    }
}
