<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremioEspecial extends Model
{
    use HasFactory;

    protected $table = 'premios_especiales';

    protected $fillable = [
        'rifa_id',
        'loteria_id',           // NUEVO
        'tipo_loteria_id',      // NUEVO
        'tipo_premio',
        'monto',
        'detalle_articulo',
        'abono_minimo',
        'fecha_premio',
        'hora_premio',
        'descripcion',
    ];

    protected $casts = [
        'fecha_premio' => 'date',
    ];

    /**
     * Relación inversa hacia Rifa
     */
    public function rifa(): BelongsTo
    {
        return $this->belongsTo(Rifa::class);
    }

    /**
     * Relación: lotería asociada a este premio
     */
    public function loteria(): BelongsTo
    {
        return $this->belongsTo(Loteria::class, 'loteria_id');
    }

    /**
     * Relación: tipo de lotería asociada a este premio
     */
    public function tipoLoteria(): BelongsTo
    {
        return $this->belongsTo(TipoLoteria::class, 'tipo_loteria_id');
    }
}
