<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLoteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'       => [
                'required', 'string', 'max:255',
                Rule::unique('loterias', 'nombre')->ignore($this->route('loteria')->id),
            ],
            'tipo_loteria' => ['required', 'exists:tipos_loteria,nombre'],
        ];
    }
}
