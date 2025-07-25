<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TipoLoteria;

class UpdateTipoLoteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Si usas route('tipos_loteria') en web.php, así está perfecto
        $tipoLoteriaId = $this->route('tipos_loteria')?->id ?? $this->tipos_loteria?->id;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
            'loteria_id' => [
                'required',
                'exists:loterias,id',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $nombre = $this->input('nombre');
            $loteria_id = $this->input('loteria_id');
            $tipoLoteriaId = $this->route('tipos_loteria')?->id ?? $this->tipos_loteria?->id;

            if (!$nombre || !$loteria_id) return;

            $nombre_normalizado = $this->normalize($nombre);

            $existe = TipoLoteria::where('loteria_id', $loteria_id)
                ->where('id', '!=', $tipoLoteriaId)
                ->get()
                ->contains(function ($tipo) use ($nombre_normalizado) {
                    return $this->normalize($tipo->nombre) === $nombre_normalizado;
                });

            if ($existe) {
                $validator->errors()->add('nombre', 'Ya existe un tipo de lotería con ese nombre en esta lotería (no se permiten duplicados por mayúsculas, espacios o acentos).');
            }
        });
    }

    private function normalize($text)
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/\s+/', '', $text);
        $text = str_replace(
            ['á','é','í','ó','ú','Á','É','Í','Ó','Ú','ä','ë','ï','ö','ü','Ä','Ë','Ï','Ö','Ü','ñ','Ñ'],
            ['a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','n','n'],
            $text
        );
        return $text;
    }
}
