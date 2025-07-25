<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Ticket;

class Cliente extends Model
{
    // Si tu tabla se llama 'clientes' no hace falta $table

    protected $fillable = [
        'cedula',
        'nombre',
        'email',
        'telefono',
        'direccion',
    ];

    // Relación: un cliente tiene muchos tickets
    public function tickets(): HasMany
    {
        // Especifica la clave foránea (cliente_id) para mayor claridad
        return $this->hasMany(Ticket::class, 'cliente_id', 'id');
    }
}
