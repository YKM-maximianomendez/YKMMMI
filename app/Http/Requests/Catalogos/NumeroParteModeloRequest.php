<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class NumeroParteModeloRequest extends FormRequest
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
            'modelo' => ['required', 'string'],
        ];

        $id_modelo = $this->route('numeroparte_modelo');

        if ($id_modelo) {
            $rules['modelo'][] = Rule::unique('manufactura_modelo', 'modelo')
                ->ignore($id_modelo, 'id_modelo');
        } else {
            $rules['modelo'][] = Rule::unique('manufactura_modelo', 'modelo');
        }

        return $rules;
    }
}
