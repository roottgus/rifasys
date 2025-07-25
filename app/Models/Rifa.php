<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rifa extends Model
{
    /**
     * Atributos asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loteria_id',
        'tipo_loteria_id',
        'nombre',
        'descripcion',
        'imagen',
        'precio',
        'cantidad_numeros',
        'fecha_sorteo',
        'hora_sorteo',
    ];

    /**
     * Casts automáticos de atributos.
     */
    protected $casts = [
        'fecha_sorteo' => 'date',         // Solo fecha (Carbon)
        'hora_sorteo'  => 'datetime:H:i', // Hora con formato H:i
    ];

    /**
     * Relación: lotería asociada a la rifa.
     */
    public function loteria(): BelongsTo
    {
        return $this->belongsTo(Loteria::class, 'loteria_id');
    }

    /**
     * Relación: tipo de lotería asociado (nuevo por ID).
     */
    public function tipoLoteria(): BelongsTo
    {
        return $this->belongsTo(TipoLoteria::class, 'tipo_loteria_id');
    }

    /**
     * Tickets asociados a la rifa.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Premios especiales de esta rifa.
     */
    public function premiosEspeciales(): HasMany
    {
        return $this->hasMany(PremioEspecial::class);
    }

    /**
 * Reglas de descuentos asociadas a la rifa.
 */
public function descuentos(): HasMany
{
    return $this->hasMany(Descuento::class);
}


    /**
     * Genera las opciones de número para la selección de tickets,
     * con padding automático según la cantidad y excluye los ya tomados.
     */
    public function generateNumberOptions(): array
    {
        $count  = (int) $this->cantidad_numeros;
        $padLen = strlen((string) ($count - 1));

        // Todos los números posibles, en formato con ceros a la izquierda
        $all = collect(range(0, $count - 1))
            ->mapWithKeys(fn(int $n) => [
                str_pad((string)$n, $padLen, '0', STR_PAD_LEFT)
                    => str_pad((string)$n, $padLen, '0', STR_PAD_LEFT)
            ]);

        // Números ya vendidos o reservados
        $taken = $this->tickets()
            ->where('estado', '!=', 'disponible')
            ->pluck('numero')
            ->map(fn($n) => str_pad((string)$n, $padLen, '0', STR_PAD_LEFT))
            ->all();

        // Excluir los tomados y devolver los restantes
        return $all->except($taken)->toArray();
    }
}
