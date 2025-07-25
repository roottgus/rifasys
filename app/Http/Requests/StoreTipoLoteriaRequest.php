<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use App\Models\TipoLoteria;

class StoreTipoLoteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'     => ['required', 'string', 'max:255'],
            'loteria_id' => ['required', 'exists:loterias,id'],
        ];
    }

    /**
     * Realiza validación extra para evitar duplicados de nombre en la misma lotería,
     * ignorando mayúsculas, espacios y acentos.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $nombreNormalizado = $this->normalize($this->input('nombre'));
            $loteriaId = $this->input('loteria_id');

            $existe = TipoLoteria::where('loteria_id', $loteriaId)
                ->get()
                ->contains(function ($tipo) use ($nombreNormalizado) {
                    return $this->normalize($tipo->nombre) === $nombreNormalizado;
                });

            if ($existe) {
                $validator->errors()->add('nombre', 'Ya existe un tipo de lotería con ese nombre en la lotería seleccionada.');
            }
        });
    }

    /**
     * Normaliza un nombre: minúsculas, sin espacios extras ni acentos.
     */
    private function normalize($string)
    {
        $str = mb_strtolower($string, 'UTF-8');
        $str = preg_replace('/\s+/', '', $str); // Quita TODOS los espacios
        // Quita tildes
        $str = strtr($str, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'Ä' => 'a', 'Ë' => 'e', 'Ï' => 'i', 'Ö' => 'o', 'Ü' => 'u',
            'ñ' => 'n', 'Ñ' => 'n'
        ]);
        return $str;
    }
}
