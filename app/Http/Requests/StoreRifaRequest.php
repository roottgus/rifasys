<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRifaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara los datos para validación.
     * Normaliza 'has_premios' y filtra solo premios válidos.
     */
    protected function prepareForValidation(): void
    {
        $has     = $this->boolean('has_premios');
        $rawPrem = $this->input('premios', []);

        // Conservamos solo los premios con tipo_loteria_id y tipo_premio
        $filtered = collect($rawPrem)
            ->filter(fn($p) =>
                !empty($p['tipo_loteria_id']) &&
                !empty($p['tipo_premio'])
            )
            ->values()
            ->toArray();

        $this->merge([
            'has_premios' => $has,
            'premios'     => $has ? $filtered : [],
        ]);
    }

    /**
     * Reglas de validación para crear una rifa.
     */
    public function rules(): array
    {
        $rules = [
            'loteria_id'        => ['required', 'exists:loterias,id'],
            'tipo_loteria_id'   => ['required', 'exists:tipos_loteria,id'],
            'nombre'            => ['required', 'string', 'max:100'],
            'descripcion'       => ['nullable', 'string'],
            'precio'            => ['required', 'numeric', 'min:0'],
            'fecha_sorteo'      => ['required', 'date'],
            'hora_sorteo'       => ['required', 'date_format:H:i'],
            'cantidad_numeros'  => ['required', 'integer', 'min:1'],
            'imagen'            => ['nullable', 'image', 'max:2048'],
            'has_premios'       => ['required', 'boolean'],
        ];

        if ($this->boolean('has_premios')) {
            $rules['premios']                        = ['required', 'array', 'min:1'];
            $rules['premios.*.tipo_loteria_id']      = ['required', 'exists:tipos_loteria,id'];
            $rules['premios.*.tipo_premio']          = ['required', 'string'];
            $rules['premios.*.monto']                = [
                'nullable',
                'numeric',
                'min:0',
                'required_if:premios.*.tipo_premio,dinero',
            ];
            $rules['premios.*.detalle_articulo']     = [
                'nullable',
                'string',
                'required_unless:premios.*.tipo_premio,dinero',
            ];
            $rules['premios.*.abono_minimo']         = ['required', 'numeric', 'min:0'];
            $rules['premios.*.fecha_premio']         = ['required', 'date'];
            $rules['premios.*.hora_premio']          = ['required', 'date_format:H:i'];
            $rules['premios.*.descripcion']          = ['nullable', 'string'];
        }

        return $rules;
    }
}
