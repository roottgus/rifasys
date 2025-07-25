<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abono extends Model
{
    /**
     * Campos asignables.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'ticket_id',
        'tipo',
        'monto',
        'metodo_pago',
        'telefono',
        'cedula',
        'titular',
        'banco',
        'referencia',
        'payment_method_id',
        'reference_number',
    ];

    /**
     * Relación: este abono pertenece a un ticket.
     *
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relación: este abono pertenece a un método de pago.
     *
     * @return BelongsTo
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
