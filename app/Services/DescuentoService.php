<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Descuento;

class DescuentoService
{
    /**
     * Calcula el mayor descuento aplicable para un cliente en una rifa,
     * sumando los tickets que ya tiene más los nuevos (proceso de compra).
     *
     * @param int $rifaId           ID de la rifa
     * @param int $clienteId        ID del cliente
     * @param int $nuevosTicketsCount  Cantidad de tickets a sumar (opcional)
     * @return float                Porcentaje de descuento (ej: 20 para 20%)
     */
    /**
 * Esta función considera la cantidad de tickets que el cliente YA tiene en la rifa,
 * más los que está comprando en este proceso.
 * Úsalo SOLO para procesos internos donde el descuento sea acumulativo por cliente.
 * Para el frontend y compras nuevas, usa la lógica directa del controller.
 */
    public static function obtenerDescuento(int $rifaId, $clienteId = null, int $nuevosTicketsCount = 0): float
{
    $totalTickets = $nuevosTicketsCount;

    // Si hay cliente, suma tickets existentes
    if ($clienteId) {
        $ticketsActuales = Ticket::where('rifa_id', $rifaId)
            ->where('cliente_id', $clienteId)
            ->count();
        $totalTickets += $ticketsActuales;
    }

    $descuento = Descuento::where('rifa_id', $rifaId)
        ->where('cantidad_minima', '<=', $totalTickets)
        ->orderByDesc('cantidad_minima')
        ->first();

    return $descuento ? (float)$descuento->porcentaje : 0.0;
}


    /**
     * Devuelve TODAS las reglas de descuento para una rifa
     * (para mostrar la tabla o información en la UI).
     *
     * @param int $rifaId
     * @return \Illuminate\Database\Eloquent\Collection|Descuento[]
     */
    public static function obtenerReglasDeRifa(int $rifaId)
    {
        return Descuento::where('rifa_id', $rifaId)
            ->orderBy('cantidad_minima')
            ->get();
    }
}
