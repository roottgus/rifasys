<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'descuentos';

    protected $fillable = [
        'rifa_id',
        'cantidad_minima',
        'porcentaje',
    ];

    // Relación: Un descuento pertenece a una rifa
    public function rifa()
    {
        return $this->belongsTo(Rifa::class);
    }
}
